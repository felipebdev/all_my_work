<?php


namespace App\Services\Mundipagg;


use App\CreditCard;
use App\Exceptions\MissingImportantChargeDataException;
use App\Logs\ChargeLog;
use App\Mail\FactoryMail;
use App\Mail\Objects\MailPayload;
use App\Mail\SendMailRecurrencePaymentFailed;
use App\Mail\SendMailRecurrencePaymentSuccess;
use App\Payment;
use App\Plan;
use App\Platform;
use App\Recurrence;
use App\Repositories\Affiliation\AffiliateRepository;
use App\Services\ChargeRulerSettings;
use App\Services\Contracts\SubscriptionServiceInterface;
use App\Services\EmailTaggedService;
use App\Services\Finances\Objects\Constants;
use App\Services\Finances\Objects\PriceTag;
use App\Services\Finances\PaymentStoreService;
use App\Services\Finances\Product\ProductInformationService;
use App\Services\Finances\Transaction\GatewayTransaction;
use App\Services\Mundipagg\Objects\OrderResult;
use App\Services\Mundipagg\Objects\PaymentData;
use App\Services\MundipaggService;
use App\Services\TransactionService;
use App\Subscriber;
use App\Transaction;
use App\Utils\TriggerIntegrationJob;
use Carbon\Carbon;
use Illuminate\Contracts\Mail\Mailable as MailableContract;
use Illuminate\Support\Facades\Log;
use MundiAPILib\APIException;
use MundiAPILib\Models\CreateCreditCardPaymentRequest;
use MundiAPILib\Models\CreateOrderRequest;
use MundiAPILib\Models\CreatePaymentRequest;
use MundiAPILib\Models\GetOrderResponse;

class CreditCardRecurrenceService
{
    use TriggerIntegrationJob;

    private SubscriptionServiceInterface $subscriptionService;

    private TransactionService $transactionService;

    protected ProductInformationService $productInformationService;

    protected AffiliateRepository $affiliateRepository;


    private ?Carbon $dueAt = null;

    private ?string $origin = null;

    private bool $skipEmail = false;

    public static $forceFailStatusDebug = false;

    /**
     * @deprecated
     */
    public static bool $useExistingOrder = false;

    /**
     * @deprecated
     */
    public static bool $logOnly = false;

    public function __construct()
    {
        $this->subscriptionService = app()->make(SubscriptionServiceInterface::class);
        $this->transactionService = app()->make(TransactionService::class);
        $this->productInformationService = app()->make(ProductInformationService::class);
        $this->affiliateRepository = app()->make(AffiliateRepository::class);
    }

    public function setDueAt(Carbon $dueAt): self
    {
        $this->dueAt = $dueAt;
        return $this;
    }

    public function skipEmail(bool $skipEmail = true): self
    {
        $this->skipEmail = $skipEmail;
        return $this;
    }

    public function setTransactionOrigin(string $origin): self
    {
        $this->origin = $origin;
        return $this;
    }

    public function createRecurrenceOrder(
        Recurrence $recurrence,
        ?Payment $originalFailedPayment = null,
        ?int $mailIdOnFail = null
    ): ?GetOrderResponse {
        ChargeLog::debug("Creating recurrence order (ID {$recurrence->id})");

        $subscriber = $recurrence->subscriber;
        $platform = $subscriber->platform;
        $defaultInstallments = $recurrence->default_installments ?? 1;

        $clientTaxTransaction = ($platform) ? ($platform->client->tax_transaction ?? 1.5) : 1.5;

        $ongoingCharge = $recurrence->current_charge + 1; // include this try on ongoing charge

        $creditCard = strlen($subscriber->credit_card_id) > 0
            ? CreditCard::where('id', '=', $subscriber->credit_card_id)->first()
            : CreditCard::where('subscriber_id', '=', $subscriber->id)->first();

        if (!$creditCard) {
            if (app()->environment('release')) {
                return null; // ignore on release
            }

            throw new MissingImportantChargeDataException("Subscriber {$subscriber->id} without card");
        }

        try {
            $orderRequest = new CreateOrderRequest();
            $orderRequest->customerId = $subscriber->customer_id;
            $orderRequest->items = $this->productInformationService->getItems($recurrence->plan, $ongoingCharge);

            $context = ChargeLog::getContext();

            $metadata = $this->productInformationService->getOrderMetadata($recurrence->plan);

            $metadata['recurrence_id'] = $recurrence->id ?? '';
            $metadata['recurrence_last_invoice'] = $recurrence->last_invoice ?? '';
            $metadata['recurrence_last_payment'] = $recurrence->last_payment ?? '';
            $metadata['obs'] = "Renovação de assinatura (recorrência {$ongoingCharge})";
            $metadata['origin'] = $this->origin ?? Transaction::ORIGIN_TRANSACTION;
            $metadata['hostname-dispatcher'] = $context['hostname-dispatcher'] ?? '';
            $metadata['hostname-runner'] = gethostname();
            $metadata['default_installments'] = $defaultInstallments;
            $metadata['affiliate_id'] = $recurrence->affiliate_id ?? null;

            $orderRequest->metadata = $metadata;

            $paymentData = $this->getPaymentRecurrence($recurrence, $creditCard->card_id, $defaultInstallments);

            $orderRequest->payments = $paymentData->getPayments();

            if (self::$useExistingOrder) {
                // option to re-use existing order from Mundipagg (due to bug)
                $orderResponse = $this->useExistingOrder($recurrence, $subscriber);

                if (is_null($orderResponse)) {
                    return null;
                }
            } else {
                if (self::$logOnly) {
                    return null;
                }

                $mundipaggService = new MundipaggService();
                $orderResponse = $mundipaggService->createClientOrder($orderRequest);
            }

            if (self::$forceFailStatusDebug) {
                $orderResponse->status = Constants::MUNDIPAGG_FAILED;
            }

            ChargeLog::info('Subscription charge/retry Mundipagg response', ['order' => $orderResponse]);

            $orderResult = OrderResult::fromMundipagg($orderResponse, $paymentData->getProducerSplits());

            $isRetry = !is_null($originalFailedPayment);
            if ($isRetry) {
                $payment = $this->updatePayment($orderResult, $originalFailedPayment);
            } else {
                $payment = $this->createPayment(
                    $orderResult,
                    $subscriber,
                    $recurrence,
                    $ongoingCharge,
                    $clientTaxTransaction
                );
            }

            $status = $orderResponse->status;
            $paymentMethod = $orderResponse->charges[0]->paymentMethod ?? null;

            ChargeLog::withContext([
                'payment_id' => $payment->id ?? null,
                'subscriber_id' => $subscriber->id ?? null,
                'payment_method' => $paymentMethod,
                'status' => $status,
            ]);

            $now = Carbon::now(); // $orderResponse->createdAt;
            if ($status == Constants::MUNDIPAGG_PAID) {
                ChargeLog::info('Subscription charge/retry: credit card successful', [
                    'successful_payment' => true,
                ]);

                $recurrence->current_charge = $ongoingCharge;
                $recurrence->last_invoice = $now;
                $recurrence->last_payment = $now;
                $recurrence->card_id = $creditCard->id;
                $recurrence->save();

                $this->handleSuccessfulPayment($platform, $subscriber, $payment, $orderResponse);
            } else { //disable subscriber
                $failures = GatewayTransaction::getOrderFailures($orderResponse);
                ChargeLog::info('Subscription charge/retry: failed (User error)', [
                    'failures' => $failures,
                    'successful_payment' => false,
                ]);

                $this->createFailedPaymentLog($platform->id, $subscriber->id, $orderResponse, $originalFailedPayment);

                $this->handleFailedPayment($payment, $recurrence->plan, $mailIdOnFail);
            }
            return $orderResponse;
        } catch (APIException $e) {
            ChargeLog::info('Subscription charge/retry: failed (API error)', [
                'payment_id' => $payment->id ?? null,
                'subscriber_id' => $subscriber->id ?? null,
                'successful_payment' => false,
            ]);

            $this->createFailedPaymentLog($platform->id, $subscriber->id, $orderResponse ?? null, $originalFailedPayment);

            if (isset($payment)) {
                $this->handleFailedPayment($payment, $recurrence->plan, $mailIdOnFail);
            } elseif (app()->bound('sentry')) {
                // payment is not even available, capture and report it to Sentry for further investigation
                app('sentry')->captureException($e);
            }

            Log::error(json_encode($e->getResponseBody()));
        }

        return null;
    }

    private function sendPaymentProof(Payment $payment)
    {
        if ($this->skipEmail) {
            return;
        }

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
    ): void {
        GatewayTransaction::createSuccessfulTransaction($platform->id, $subscriber->id, $result, $this->origin,
            $payment->id);

        $this->subscriptionService->enableSubscriptionByPayment($payment);

        $this->sendPaymentProof($payment);

        $this->triggerPaymentApprovedEvent($payment);
    }

    private function handleFailedPayment(
        Payment $payment,
        Plan $plan,
        ?int $mailIdOnFail = null
    ) {
        $platformId = $plan->platform_id;
        $subscriber = $payment->subscriber;

        $this->markFailedPayment($payment); // mark only (payment status = 'failed')

        $this->updateProductStatus($subscriber, $plan, $payment, $mailIdOnFail);

        //$this->sendFailedPaymentEmail($platformId, $subscriber, $plan, $payment, $mailIdOnFail);
    }

    private function markFailedPayment(Payment $payment): void
    {
        $payment->status = Payment::STATUS_FAILED;
        $payment->save();
    }

    private function updateProductStatus(
        Subscriber $subscriber,
        Plan $plan,
        Payment $payment,
        ?int $mailIdOnFail = null
    ): void {
        $shouldCancelSubscription = ChargeRulerSettings::isCancelRequired($mailIdOnFail);

        if ($shouldCancelSubscription) {
            $cancellationReason = 'Cancelado automaticamente pela régua de cobrança';

            ChargeLog::info('Subscription charge/retry: subscription cancellation', [
                'subscriber_id' => $subscriber->id ?? null,
                'plan_id' => $plan->id ?? null,
            ]);

            // set Recurrence as "canceled"
            $this->subscriptionService->cancelSubscription($subscriber, $plan, $cancellationReason);

            $this->triggerSubscriptionCanceledEvent($payment);
        } else {
            ChargeLog::info('Subscription charge/retry: marking as failed', [
                'subscriber_id' => $subscriber->id ?? null,
                'plan_id' => $plan->id ?? null,
            ]);

            $this->subscriptionService->markSubscriptionWithFailedPayment($subscriber, $plan);
        }
    }

    private function sendFailedPaymentEmail(
        string $platformId,
        Subscriber $subscriber,
        Plan $plan,
        Payment $payment,
        ?int $mailIdOnFail = null
    ): void {
        if ($this->skipEmail) {
            return;
        }

        ChargeLog::info('Subscription charge/retry: failed charge: email notification', [
            'platform_id' => $platformId ?? null,
            'subscriber_id' => $subscriber->id ?? null,
            'plan_id' => $plan->id ?? null,
            'mail_id' => $mailIdOnFail ?? null,
        ]);

        $mail = $this->getFailedPaymentMail($payment, $mailIdOnFail);
        EmailTaggedService::mail($platformId, 'CHARGE_RULER', $mail);
    }

    private function getFailedPaymentMail(Payment $payment, ?int $mailId = null): MailableContract
    {
        if (is_null($mailId)) {
            return new SendMailRecurrencePaymentFailed($payment->platform_id, $payment->subscriber, $payment);
        }

        $mailPayload = new MailPayload($payment->platform_id, [
            'subscriber' => $payment->subscriber ?? null,
            'payment' => $payment ?? null
        ]);

        return FactoryMail::build($mailId, $mailPayload);
    }

    public function getPaymentRecurrence(
        Recurrence $recurrence,
        string $cardId,
        int $installments = 1
    ): PaymentData {
        $platform = $recurrence->subscriber->platform;
        $plan = $recurrence->plan;
        $ongoingChargeNumber = $recurrence->current_charge + 1;
        $affiliate = $recurrence->affiliate ?? null;

        //Order payments
        $payment = new CreatePaymentRequest();
        $payment->paymentMethod = Constants::MUNDIPAGG_PAYMENT_METHOD_CREDIT_CARD;
        $payment->creditCard = new CreateCreditCardPaymentRequest();
        $payment->creditCard->installments = $installments;
        $payment->creditCard->cardId = $cardId;

        //Sum items amount
        $amount = $this->productInformationService->getTotalAmountForRecurrence($plan, $ongoingChargeNumber);
        $amountWithInterest = $installments * $plan->getInstallmentValue($amount, $installments);

        $payment->amount = number_format($amountWithInterest, 0, '', '');

        $planPriceTag = PriceTag::fromInt($plan->id, $amount);
        $orderBumpPriceTags = [];

        //get payment split
        $splitService = new SplitService($platform->id);

        if ($affiliate) {
            $splitService->withAffiliate($affiliate);
        }

        $producerSplit = $splitService->getPaymentSplit(
            $amount / 100,
            $amountWithInterest / 100,
            $planPriceTag,
            $orderBumpPriceTags,
            $installments
        );

        $mundipaggSplitService = new MundipaggSplitService($platform->id);

        $payment->split = $mundipaggSplitService->generateMundipaggSplit($producerSplit);
        $payment->metadata = $producerSplit->getMetadata();

        return PaymentData::pack([$producerSplit], [$payment]);
    }

    private function createPayment(
        OrderResult $orderResult,
        Subscriber $subscriber,
        Recurrence $recurrence,
        int $installment,
        float $clientTaxTransaction
    ): Payment {
        $paymentStore = new PaymentStoreService();
        $paymentStore->setPaymentSource(Payment::PAYMENT_SOURCE_AUTOMATIC);
        if ($this->dueAt) {
            $paymentStore->setDueDate($this->dueAt);
        }
        $payments = $paymentStore->storePayments(
            $subscriber,
            $orderResult,
            Carbon::now(),
            $recurrence->order_number,
            $clientTaxTransaction,
            $installment,
        );

        $payment = $payments->first(); // recurrence has a single payment

        $payment->recurrences()->attach($recurrence);

        return $payment;
    }

    private function updatePayment(OrderResult $response, Payment $originalFailedPayment): Payment
    {
        $response = $response->getMundipaggOrderResponse();

        $paymentStatus = $response->status;

        $isPaid = $paymentStatus == Constants::MUNDIPAGG_PAID;

        $originalFailedPayment->confirmed_at = $isPaid ? Carbon::now() : null;
        $originalFailedPayment->status = $paymentStatus;
        $originalFailedPayment->order_id = $response->id;

        $charges = $response->charges ?? [];
        foreach ($charges as $charge) {
            $originalFailedPayment->charge_id = $charge->id;
            $originalFailedPayment->charge_code = $charge->code;
        }

        $originalFailedPayment->save();

        $paymentPlanStatus = $paymentStatus; // copy status from payment

        // update related payment_plans
        $paymentPlans = $originalFailedPayment->plans();
        $paymentPlans->updateExistingPivot($paymentPlans->allRelatedIds(), ['status' => $paymentPlanStatus]);

        return $originalFailedPayment;
    }

    private function createFailedPaymentLog(
        $platformId,
        $subscriberId,
        $orderResponse,
        ?Payment $originalFailedPayment
    ): void {
        GatewayTransaction::createFailedTransaction(
            $platformId,
            $subscriberId,
            $orderResponse ?? null,
            $this->origin ?? null,
            $originalFailedPayment->id ?? null
        );
    }

    private function useExistingOrder(Recurrence $recurrence, Subscriber $subscriber)
    {
        $mundipaggService = new MundipaggService();

        $ignored = [Recurrence::PAYMENT_METHOD_BOLETO, Recurrence::PAYMENT_METHOD_PIX];
        if (in_array($recurrence->payment_method, $ignored)) {
            ChargeLog::info("Ignoring boleto/PIX");
            return null;
        }

        ChargeLog::info("Using existing order");

        $orderResponses = $mundipaggService->getClient()->getOrders()->getOrders(
            $page = null,
            $size = null,
            $code = null,
            $status = null,
            $createdSince = Carbon::now(),
            $createdUntil = Carbon::now()->addHours(1),
            $customerId = $subscriber->customer_id
        );

        $responses = collect($orderResponses->data);

        $orderResponse = $responses
            ->where('metadata.recurrence_id', '=', $recurrence->id)
            ->whereIn('metadata.origin', ['regular_charge'])
            ->first();

        sleep(0.4);

        if (is_null($orderResponse)) {
            ChargeLog::info("No existing order found");
            return null;
        } else {
            ChargeLog::info("Using existing order {$orderResponse->code}",
                $orderResponse->metadata ?? []
            );
        }

        if (self::$logOnly) {
            return null;
        }

        return $orderResponse;
    }


}
