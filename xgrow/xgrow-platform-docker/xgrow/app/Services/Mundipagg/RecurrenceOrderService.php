<?php


namespace App\Services\Mundipagg;


use App\CreditCard;
use App\Http\Controllers\Mundipagg\MundipaggCheckoutController;
use App\Http\Controllers\Mundipagg\MundipaggExceptionController;
use App\Logs\ChargeLog;
use App\Mail\SendMailRecurrencePaymentFailed;
use App\Mail\SendMailRecurrencePaymentSuccess;
use App\Payment;
use App\Plan;
use App\Platform;
use App\Recurrence;
use App\Services\Contracts\SubscriptionServiceInterface;
use App\Services\EmailTaggedService;
use App\Services\MundipaggService;
use App\Services\TransactionService;
use App\Subscriber;
use App\Utils\TriggerIntegrationJob;
use Carbon\Carbon;
use Illuminate\Contracts\Mail\Mailable as MailableContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use MundiAPILib\APIException;
use MundiAPILib\Models\CreateCreditCardPaymentRequest;
use MundiAPILib\Models\CreateOrderRequest;
use MundiAPILib\Models\CreatePaymentRequest;
use MundiAPILib\Models\GetOrderResponse;

class RecurrenceOrderService extends CheckoutOrderService
{
    use TriggerIntegrationJob;

    /**
     * @var SubscriptionServiceInterface
     */
    private $subscriptionService;

    private $transactionService;

    public function __construct()
    {
        $this->subscriptionService = app()->make(SubscriptionServiceInterface::class);
        $this->transactionService = app()->make(TransactionService::class);
    }

    public function createRecurrenceOrder(
        Recurrence $recurrence,
        ?MailableContract $mailOnFail = null,
        ?Payment $originalFailedPayment = null,
        bool $cancelSubscriptionOnFail = false,
        bool $dryRun = false,
        ?string $baseDate = null
    ) {
        $subscriber = $recurrence->subscriber;
        $platform = $subscriber->platform;

        $clientTaxTransaction = ($platform) ? ($platform->client->tax_transaction ?? 1.5) : 1.5;

        if ($dryRun) {
            $info = [
                Carbon::now()->toDateTimeString(),
                $baseDate ?? Carbon::now()->toDateString(),
                $platform->name ?? '',
                $subscriber->name ?? '',
                $subscriber->email ?? '',
                $originalFailedPayment->id ?? '',
                $originalFailedPayment->order_code ?? '',
                $originalFailedPayment->payment_date ?? '',
            ];

            echo 'Writing to subscription-charge-log.csv: '.join(';', $info);
            Storage::disk('local')->append('subscription-charge-log.csv',  join(';', $info));

            return;
        }

        try {
            $request = new CreateOrderRequest();
            $request->customerId = $subscriber->customer_id;
            $parcel_number = $recurrence->current_charge + 1;
            $request->items = $this->getItems($recurrence->plan, $parcel_number);

            //get subscriber credit card
            if (strlen($subscriber->credit_card_id) > 0) {
                $creditCard = CreditCard::where('id', '=', $subscriber->credit_card_id)->first();
            } else {
                $creditCard = CreditCard::where('subscriber_id', '=', $subscriber->id)->first();
            }

            $request->metadata = $this->getOrderMetadata($recurrence->plan);

            $request->payments = array($this->getPaymentRecurence($platform, $recurrence->plan, $creditCard->card_id, $parcel_number));

            $mundipaggService = new MundipaggService($platform->id);
            $result = $mundipaggService->getClient()->getOrders()->createOrder($request);

            ChargeLog::info('Subscription charge/retry Mundipagg response', ['order' => $result]);

            $payment = $this->storePayment(
                Payment::PAYMENT_SOURCE_AUTOMATIC,
                $subscriber,
                $result,
                Carbon::now(),
                $recurrence->order_number,
                $clientTaxTransaction,
                $this->getOrderBumps(),
                $originalFailedPayment
            );
            $payment->recurrences()->attach($recurrence);

            //upate recurrence
            if ($result->status == 'paid') {
                ChargeLog::info('Subscription charge/retry successful', [
                    'recurrence_id' => $recurrence->id ?? null,
                    'payment_id' => $payment->id ?? null,
                    'subscriber_id' => $subscriber->id ?? null
                ]);

                $recurrence->current_charge = $parcel_number;
                $recurrence->last_payment = $result->createdAt;
                $recurrence->card_id = $creditCard->id;
                $recurrence->save();

                $this->handleSuccessfulPayment($platform, $subscriber, $payment, $result);
            } else { //disable subscriber
                ChargeLog::info('Subscription charge/retry failed (User error)', [
                    'recurrence_id' => $recurrence->id ?? null,
                    'payment_id' => $payment->id ?? null,
                    'subscriber_id' => $subscriber->id ?? null,
                ]);

                MundipaggExceptionController::createFailedTransaction(
                    $platform->id,
                    $subscriber->id,
                    $result,
                    $originalFailedPayment->id
                );

                $mail = $mailOnFail ?? new SendMailRecurrencePaymentFailed($platform->id, $subscriber, $payment);
                $this->handleFailedPayment($payment, $subscriber, $recurrence->plan, $mail, $cancelSubscriptionOnFail);
            }
            return $result;

        } catch (APIException $e) {
            ChargeLog::info('Subscription charge/retry failed (API error)', [
                'recurrence_id' => $recurrence->id ?? null,
                'payment_id' => $payment->id ?? null,
                'subscriber_id' => $subscriber->id ?? null,
            ]);

            $mail = $mailOnFail ?? new SendMailRecurrencePaymentFailed($platform->id, $subscriber, $payment);
            $this->handleFailedPayment($payment, $subscriber, $recurrence->plan, $mail, $cancelSubscriptionOnFail);
            Log::error(json_encode($e->getResponseBody()));
        }
    }

    private function sendPaymentProof(Payment $payment)
    {
        $platformId = $payment->platform->id;
        $subscriber = $payment->subscriber;

        $mail = new SendMailRecurrencePaymentSuccess($platformId, $subscriber, $payment);

        EmailTaggedService::mail($platformId, 'CHARGE_RULER', $mail);
    }

    private function handleSuccessfulPayment(
        Platform $platform,
        Subscriber $subscriber,
        Payment $payment,
        GetOrderResponse $result
    ): void
    {
        MundipaggExceptionController::createSuccessfulTransaction($platform->id, $subscriber->id, $result, $payment->id);

        $this->sendPaymentProof($payment);

        $this->triggerPaymentApprovedEvent($payment);
    }

    private function handleFailedPayment(
        Payment $payment,
        Subscriber $subscriber,
        Plan $plan,
        MailableContract $mail,
        bool $shouldCancelSubscription = false
    ) {
        $platformId = $plan->platform_id;

        if ($shouldCancelSubscription) {
            ChargeLog::info('Subscription charge/retry failed cancellation', [
                'subscriber_id' => $subscriber->id ?? null,
                'plan_id' => $plan->id ?? null,
            ]);

            $this->subscriptionService->cancelSubscription($subscriber, $plan);

            $this->triggerSubscriptionCanceledEvent($payment);
        } else {
            ChargeLog::info('Subscription charge/retry failed marking', [
                'subscriber_id' => $subscriber->id ?? null,
                'plan_id' => $plan->id ?? null,
            ]);

            $this->subscriptionService->markSubscriptionWithFailedPayment($subscriber, $plan);
        }

        ChargeLog::info('Subscription charge/retry failed notification', [
            'platform_id' => $platformId ?? null,
            'subscriber_id' => $subscriber->id ?? null,
            'plan_id' => $plan->id ?? null,
            'mailable' => $mail ?? null,
        ]);

        EmailTaggedService::mail($platformId, 'CHARGE_RULER', $mail);
    }

    public function getPaymentRecurence(Platform $platform, Plan $plan, $credit_card_id, $parcel_number = 1)
    {
        //Order payments
        $payment = new CreatePaymentRequest();
        $payment->paymentMethod = MundipaggCheckoutController::PAYMENT_METHOD_CREDIT_CARD;

        $payment->creditCard = new CreateCreditCardPaymentRequest();
        $payment->creditCard->installments = 1;
        $payment->creditCard->cardId = $credit_card_id;
        $payment->metadata = $this->getOrderMetadata($plan);

        //Sum items amount
        $amount = $this->getTotalAmount(new Request, $plan, $parcel_number);
        $amountWithInterest = $plan->getInstallmentValue($amount);
        $payment->amount = str_replace('.','',(string) number_format($amountWithInterest, 2, '.', '.'));

        //get payment split
        $splitService = new SplitService($platform->id);
        $split = $splitService->getPaymentSplit($amountWithInterest, $amount);
        $payment->split = $split;
        $payment->metadata = $splitService->getPaymentMetadata();

        return $payment;
    }


}
