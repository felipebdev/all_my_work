<?php

namespace App\Services\Charges;

use App\Coupon;
use App\CreditCard;
use App\Http\Controllers\Mundipagg\MundipaggExceptionController;
use App\Jobs\MundipaggUnlimitedOrderRetry;
use App\Logs\ChargeLog;
use App\Mail\SendMailRecurrencePaymentFailed;
use App\Mail\SendMailRecurrencePaymentSuccess;
use App\Payment;
use App\Services\Contracts\SubscriptionServiceInterface;
use App\Services\EmailTaggedService;
use App\Services\Mundipagg\SplitService;
use App\Services\MundipaggService;
use App\Subscriber;
use Carbon\Carbon;
use App\Utils\TriggerIntegrationJob;
use Exception;
use Illuminate\Contracts\Mail\Mailable as MailableContract;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use MundiAPILib\APIException;
use MundiAPILib\Models\CreateCreditCardPaymentRequest;
use MundiAPILib\Models\CreateOrderItemRequest;
use MundiAPILib\Models\CreateOrderRequest;
use MundiAPILib\Models\CreatePaymentRequest;

class NoLimitChargeService
{
    use TriggerIntegrationJob;

    private $subscriptionService;

    public function __construct(SubscriptionServiceInterface $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * @param  \App\Payment  $payment
     * @param  \Illuminate\Contracts\Mail\Mailable|null  $mailOnFail  Send specific email if charge fails
     * @param  bool|null  $cancelSubscriptionOnFail
     */
    public function createPaymentOrder(
        Payment $payment,
        ?MailableContract $mailOnFail = null,
        bool $cancelSubscriptionOnFail = false,
        bool $dryRun = false,
        ?string $baseDate = null
    ): void {
        if ($dryRun) {
            $info = [
                Carbon::now()->toDateTimeString(),
                $baseDate ?? Carbon::now()->toDateString(),
                $payment->platform->name ?? '',
                $payment->subscriber->name ?? '',
                $payment->subscriber->email ?? '',
                $payment->id ?? '',
                $payment->order_code ?? '',
                $payment->payment_date ?? '',
            ];

            echo 'Wrote to no-limit-charge-log.csv: '.join(';', $info);
            Storage::disk('local')->append('no-limit-charge-log.csv', join(';', $info));

            return;
        }

        try {
            $orderRequest = new CreateOrderRequest();
            $orderRequest->closed = true;
            $orderRequest->customerId = $payment->customer_id;

            $metadata['obs'] = "Venda sem limite (parcela ".$payment->installment_number." de ".$payment->installments.")";
            $metadata['unlimited_sale'] = true;
            $metadata['total_installments'] = $payment->installments;
            if (strlen($payment->coupon_id)) {
                try {
                    $coupon = Coupon::findOrFail($payment->coupon_id);
                    $metadata['cupom_id'] = $coupon->id;
                    $metadata['cupom_id'] = $coupon->code;
                } catch (Exception $e) {
                }
            }

            $items = array();
            $orderbumpId = null;
            foreach ($payment->plans as $cod => $plan) {
                $itemPrice = new CreateOrderItemRequest();
                $itemPrice->description = $plan->name;
                $itemPrice->quantity = 1;
                $itemPrice->amount = str_replace('.', '', $plan->getPrice());
                $itemPrice->code = $plan->id;
                $items[] = $itemPrice;

                if (($plan->pivot->type == 'order_bump') ||
                    (!empty($orderbumpId) && $orderbumpId == $plan->id)
                ) {
                    $metadata['order_bump_plan_id'] = $plan->id;
                    $metadata['order_bump_plan'] = $plan->name;
                    $metadata['order_bump_value'] = $plan->price;
                } else {
                    $metadata['plan_id'] = $plan->id;
                    $metadata['plan'] = $plan->name;
                    $metadata['value'] = $plan->price;
                }

                $orderbumpId = $plan->order_bump_plan_id;
            }
            $orderRequest->items = $items;
            $orderRequest->metadata = $metadata;

            $paymentRequest = new CreatePaymentRequest();
            $paymentRequest->paymentMethod = "credit_card";
            $paymentRequest->amount = str_replace('.', '', $payment->price);
            $paymentRequest->creditCard = new CreateCreditCardPaymentRequest();
            $paymentRequest->creditCard->cardId = CreditCard::findOrFail($payment->subscriber->credit_card_id)->card_id;

            $splitService = new SplitService($payment->platform_id);
            $split = $splitService->getPaymentSplit(
                $payment->price,
                $payment->plans_value,
                $payment->installments,
                false
            );
            $paymentRequest->split = $split;
            $paymentRequest->metadata = $splitService->getPaymentMetadata();
            $orderRequest->payments = array($paymentRequest);
            $mundipaggService = new MundipaggService($payment->platform_id);
            $order = $mundipaggService->getClient()->getOrders()->createOrder($orderRequest);

            ChargeLog::info('Mundipagg No Limit response', ['order' => $order]);

            $payment->status = $order->status;
            $payment->order_id = $order->id;
            $payment->customer_id = $order->customer->id;
            $payment->order_code = $order->code;

            if ($order->status == 'paid') {
                ChargeLog::info('Successful No Limit payment', [
                    'payment_id' => $payment->id ?? null,
                ]);

                foreach ($order->charges as $cod => $charge) {
                    $payment->charge_id = $charge->id;
                    $payment->charge_code = $charge->code;
                    if ($charge->lastTransaction) {
                        if ($charge->lastTransaction->split) {
                            foreach ($charge->lastTransaction->split as $c => $split) {
                                //Xgrow
                                if ($split->options->chargeProcessingFee == true) {
                                    $payment->service_value = $split->amount / 100;
                                } else { //Customer
                                    $payment->customer_value = $split->amount / 100;
                                }
                            }
                        }
                    }
                }
                $this->handleSuccessPayment($payment);
            } else {
                ChargeLog::info('Failed No Limit payment', [
                    'payment_id' => $payment->id ?? null,
                ]);

                MundipaggExceptionController::createFailedTransaction(
                    $payment->platform_id,
                    $payment->subscriber,
                    $order
                );

                $mail = $mailOnFail
                    ?? new SendMailRecurrencePaymentFailed($payment->platform_id, $payment->subscriber, $payment);

                $this->handleFailedPayment($payment, $mail, $cancelSubscriptionOnFail);
            }
            $payment->save();
        } catch (APIException $e) {
            ChargeLog::info('Failed recurrence payment (API error)', [
                'payment_id' => $payment->id ?? null,
            ]);

            $mail = $mailOnFail
                ?? new SendMailRecurrencePaymentFailed($payment->platform_id, $payment->subscriber, $payment);

            $this->handleFailedPayment($payment, $mail, $cancelSubscriptionOnFail);
            Log::error(json_encode($e->getResponseBody()));
        }
    }

    private function handleSuccessPayment(Payment $payment)
    {
        $platformId = $payment->platform_id;
        $subscriber = $payment->subscriber;

        $mail = new SendMailRecurrencePaymentSuccess($platformId, $subscriber, $payment);

        EmailTaggedService::mail($platformId, 'CHARGE_RULER', $mail);

        $this->triggerPaymentApprovedEvent($payment);
    }

    /**
     * Retry charge of a failed payment
     *
     * @param  string|null  $baseDate
     * @param  \App\Payment  $payment
     * @param  bool  $cancelSubscriptionOnFail
     */
    public function retryChargePayment(
        Payment $payment,
        MailableContract $mailOnFail,
        bool $cancelSubscriptionOnFail = false,
        bool $dryRun = false,
        ?string $baseDate = null
    ): bool {
        if (!$this->shouldRetryCharge($payment)) {
            return false;
        }

        Payment::where('id', $payment->id)->increment('attempts');

        ChargeLog::info('No-Limit retry dispatched', ['payment_id' => $payment->id ?? null]);

        MundipaggUnlimitedOrderRetry::dispatch($payment, $mailOnFail, $cancelSubscriptionOnFail, $dryRun, $baseDate);
        return true;
    }

    /**
     * Handle failed payment
     *
     * @param  \App\Payment  $payment
     * @param  \Illuminate\Contracts\Mail\Mailable  $mail Mail to send if user's transaction fails
     * @param  bool  $shouldCancelSubscription  If true, cancel subscriber's plan if user's transaction fails (default false)
     */
    private function handleFailedPayment(
        Payment $payment,
        MailableContract $mail,
        bool $shouldCancelSubscription = false
    ) {
        $payment->status = Payment::STATUS_FAILED;
        $payment->save();

        $platformId = $payment->platform_id;
        $subscriber = $payment->subscriber;

        foreach ($payment->plans as $cod => $plan) {
            if ($shouldCancelSubscription) {
                ChargeLog::info('No-Limit charge/retry failed cancellation', [
                    'subscriber_id' => $subscriber->id ?? null,
                    'plan_id' => $plan->id ?? null,
                ]);

                $this->subscriptionService->cancelSubscription($subscriber, $plan);

                $this->triggerSubscriptionCanceledEvent($payment);
            } else {
                ChargeLog::info('No-Limit charge/retry failed marking', [
                    'subscriber_id' => $subscriber->id ?? null,
                    'plan_id' => $plan->id ?? null,
                ]);

                $this->subscriptionService->markSubscriptionWithFailedPayment($subscriber, $plan);
            }

            ChargeLog::info('No-Limit charge/retry failed notification', [
                'platform_id' => $platformId ?? null,
                'subscriber_id' => $subscriber->id ?? null,
                'plan_id' => $plan->id ?? null,
                'mailable' => $mail ?? null,
            ]);

            EmailTaggedService::mail($platformId, 'CHARGE_RULER', $mail);
        }
    }

    private function shouldRetryCharge(Payment $payment): bool
    {
        $subscriber = $payment->subscriber;
        if ($subscriber->status != Subscriber::STATUS_ACTIVE) {
            ChargeLog::info('No-Limit retry ignored: subscriber is not active', [
                'payment_id' => $payment->id ?? null,
                'subscriber_id' => $subscriber->id ?? null,
            ]);
            return false;
        }

        if ($payment->type != Payment::TYPE_UNLIMITED) {
            ChargeLog::info('No-Limit retry ignored: wrong payment type', [
                'payment_id' => $payment->id ?? null,
                'subscriber_id' => $subscriber->id ?? null,
            ]);
            return false;
        }

        if ($payment->status != Payment::STATUS_FAILED) {
            ChargeLog::info('No-Limit retry ignored: payment is not failed', [
                'payment_id' => $payment->id ?? null,
                'subscriber_id' => $subscriber->id ?? null,
            ]);
            return false;
        }

        if ($payment->type_payment != Payment::TYPE_PAYMENT_CREDIT_CARD) {
            ChargeLog::info('No-Limit retry ignored: not a credit card', [
                'payment_id' => $payment->id ?? null,
                'subscriber_id' => $subscriber->id ?? null,
            ]);
            return false;
        }
        return true;
    }
}
