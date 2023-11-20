<?php

namespace App\Services\Finances\Payment\Manual;

use App\Facades\Whatsapp;
use App\Logs\ChargeLog;
use App\Mail\SendMailRecurrencePaymentSuccess;
use App\Payment;
use App\PaymentPlan;
use App\Platform;
use App\Recurrence;
use App\Repositories\Affiliation\AffiliateRepository;
use App\Services\Contracts\SubscriptionServiceInterface;
use App\Services\EmailTaggedService;
use App\Services\Finances\Objects\Constants;
use App\Services\Finances\Objects\PriceTag;
use App\Services\Finances\Payment\Strategies\BoletoOrder;
use App\Services\Finances\Payment\Strategies\PixOrder;
use App\Services\Finances\PaymentStoreService;
use App\Services\Finances\Product\ProductInformationService;
use App\Services\Finances\Product\ProductPaymentService;
use App\Services\Finances\Subscriber\SubscriberCreditCard;
use App\Services\Finances\Transaction\GatewayTransaction;
use App\Services\Mundipagg\MundipaggSplitService;
use App\Services\Mundipagg\Objects\OrderResult;
use App\Services\Mundipagg\Objects\PaymentData;
use App\Services\Mundipagg\SplitService;
use App\Services\MundipaggService;
use App\Services\TransactionService;
use App\Subscriber;
use App\Transaction;
use App\Utils\TriggerIntegrationJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use MundiAPILib\APIException;
use MundiAPILib\Models\CreateBoletoPaymentRequest;
use MundiAPILib\Models\CreateCreditCardPaymentRequest;
use MundiAPILib\Models\CreateOrderRequest;
use MundiAPILib\Models\CreatePaymentRequest;
use MundiAPILib\Models\CreatePixPaymentRequest;
use MundiAPILib\Models\GetOrderResponse;

class StudentPaymentService
{
    use TriggerIntegrationJob;

    private SubscriptionServiceInterface $subscriptionService;

    private TransactionService $transactionService;

    protected ProductInformationService $productInformationService;

    protected AffiliateRepository $affiliateRepository;

    protected BoletoOrder $boletoOrder;

    protected PixOrder $pixOrder;

    private ?Carbon $dueAt = null;

    private bool $skipEmail = false;

    public static bool $forceFailStatusDebug = false;

    private $origin;

    public function __construct()
    {
        $this->subscriptionService = app()->make(SubscriptionServiceInterface::class);
        $this->transactionService = app()->make(TransactionService::class);
        $this->productInformationService = app()->make(ProductInformationService::class);
        $this->affiliateRepository = app()->make(AffiliateRepository::class);
        $this->boletoOrder = app()->make(BoletoOrder::class);
        $this->pixOrder = app()->make(PixOrder::class);
    }

    public function createRecurrenceOrder(Recurrence $recurrence, $paymentMethod, array $ccInfo = []): ?GetOrderResponse
    {
        ChargeLog::debug("Creating recurrence order (ID {$recurrence->id})");

        $subscriber = $recurrence->subscriber;
        $platform = $subscriber->platform;
        $defaultInstallments = $ccInfo['installment'] ?? 1;
        $cardToken = $ccInfo['token'] ?? null;
        $this->origin = Transaction::ORIGIN_PAYMENT_AREA;

        $clientTaxTransaction = ($platform) ? ($platform->client->tax_transaction ?? 1.5) : 1.5;

        $ongoingCharge = $recurrence->current_charge + 1; // include this try on ongoing charge

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
            $metadata['origin'] = Transaction::ORIGIN_LEARNING_AREA;
            $metadata['hostname-dispatcher'] = $context['hostname-dispatcher'] ?? '';
            $metadata['hostname-runner'] = gethostname();
            $metadata['default_installments'] = $defaultInstallments;
            $metadata['affiliate_id'] = $recurrence->affiliate_id ?? null;

            $orderRequest->metadata = $metadata;

            $paymentData = $this->getPaymentRecurrence($recurrence, $paymentMethod, $cardToken, $defaultInstallments);

            $orderRequest->payments = $paymentData->getPayments();

            $mundipaggService = new MundipaggService();

            ChargeLog::warning("Creating new order");

            $orderResponse = $mundipaggService->createClientOrder($orderRequest);

            if (self::$forceFailStatusDebug) {
                $orderResponse->status = Constants::MUNDIPAGG_FAILED;
            }

            ChargeLog::info('Subscription charge/retry Mundipagg response', ['order' => $orderResponse]);

            $orderResult = OrderResult::fromMundipagg($orderResponse, $paymentData->getProducerSplits());

            $payment = $this->createPayment(
                $orderResult,
                $subscriber,
                $recurrence,
                $ongoingCharge,
                $clientTaxTransaction
            );

            $status = $orderResponse->status;
            $paymentMethod = $orderResponse->charges[0]->paymentMethod ?? null;

            ChargeLog::withContext([
                'payment_id' => $payment->id ?? null,
                'subscriber_id' => $subscriber->id ?? null,
                'payment_method' => $paymentMethod,
                'status' => $status,
            ]);

            $now = Carbon::now(); // $orderResponse->createdAt;
            if ($status == Constants::MUNDIPAGG_PAID && $paymentMethod == 'credit_card') {
                ChargeLog::info('Subscription charge/retry: credit card successful', [
                    'successful_payment' => true,
                ]);

                //Store credit card
                foreach ($orderResponse->charges ?? [] as $cod => $charge) {
                    if ($charge->paymentMethod == Constants::MUNDIPAGG_PAYMENT_METHOD_CREDIT_CARD) {
                        $card = $charge->lastTransaction->card ?? null;
                        if ($card) {
                            $creditCard = SubscriberCreditCard::save($subscriber, $card);
                        }
                    }
                }

                $recurrence->payment_method = 'credit_card';
                $recurrence->current_charge = $ongoingCharge;
                $recurrence->last_invoice = $now;
                $recurrence->last_payment = $now;
                $recurrence->card_id = $creditCard->id ?? null;
                $recurrence->save();

                $this->handleSuccessfulPayment($platform, $subscriber, $payment, $orderResponse);
            } elseif ($status == Constants::MUNDIPAGG_PENDING && $paymentMethod == 'boleto') {
                ChargeLog::info('Subscription charge/retry: boleto sent', ['successful_payment' => true]);

                $recurrence->payment_method = 'boleto';
                $recurrence->current_charge = $ongoingCharge;
                $recurrence->last_invoice = $now;
                //$recurrence->last_payment = null;
                $recurrence->save();

                $this->handleBoletoCreated($payment, $subscriber);
            } elseif ($status == Constants::MUNDIPAGG_PENDING && $paymentMethod == 'pix') {
                ChargeLog::info('Subscription charge/retry: PIX sent', ['successful_payment' => true]);

                $recurrence->payment_method = 'pix';
                $recurrence->current_charge = $ongoingCharge;
                $recurrence->last_invoice = $now;
                //$recurrence->last_payment = null;
                $recurrence->save();

                $this->handlePixCreated($payment, $subscriber);
            } else {
                $failures = GatewayTransaction::getOrderFailures($orderResponse);
                ChargeLog::info('Subscription charge/retry: failed (User error)', [
                    'failures' => $failures,
                    'successful_payment' => false,
                ]);

                $this->createFailedPaymentLog($platform->id, $subscriber->id, $orderResponse);

                $this->handleFailedPayment($payment);
            }

            return $orderResponse;
        } catch (APIException $e) {
            ChargeLog::info('Subscription charge/retry: failed (API error)', [
                'payment_id' => $payment->id ?? null,
                'subscriber_id' => $subscriber->id ?? null,
                'successful_payment' => false,
            ]);

            $this->createFailedPaymentLog($platform->id, $subscriber->id, $orderResponse ?? null);

            if (isset($payment)) {
                $this->handleFailedPayment($payment);
            } elseif (app()->bound('sentry')) {
                // payment is not even available, capture and report it to Sentry for further investigation
                app('sentry')->captureException($e);
            }

            Log::error(json_encode($e->getResponseBody()));

            return null;
        }
    }

    public function getPaymentRecurrence(
        Recurrence $recurrence,
        string $paymentMethod,
        ?string $cardToken = null,
        int $installments = 1
    ): PaymentData {
        $platform = $recurrence->subscriber->platform;
        $plan = $recurrence->plan;
        $ongoingChargeNumber = $recurrence->current_charge + 1;
        $affiliate = $recurrence->affiliate ?? null;

        if ($cardToken <> '' || is_null($paymentMethod)) {
            $paymentMethod = Constants::MUNDIPAGG_PAYMENT_METHOD_CREDIT_CARD;
        }

        //Order payments
        $payment = new CreatePaymentRequest();
        $payment->paymentMethod = $paymentMethod;

        if ($paymentMethod == Constants::MUNDIPAGG_PAYMENT_METHOD_CREDIT_CARD) {
            $payment->creditCard = new CreateCreditCardPaymentRequest();
            $payment->creditCard->installments = $installments;
            $payment->creditCard->cardToken = $cardToken;
        } elseif ($paymentMethod == Constants::MUNDIPAGG_PAYMENT_METHOD_BOLETO) {
            $payment->boleto = new CreateBoletoPaymentRequest();
            $payment->boleto->instructions = "Pagar até o vencimento";
            $payment->boleto->dueAt = $this->dueAt ?? ProductPaymentService::boletoCheckoutDueAt($plan);
        } elseif ($paymentMethod == Constants::MUNDIPAGG_PAYMENT_METHOD_PIX) {
            $payment->pix = new CreatePixPaymentRequest();
            $payment->pix->expiresAt = $this->dueAt ?? ProductPaymentService::pixExpiresAt($plan);
        }

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
        $paymentStore->setPaymentSource(Payment::PAYMENT_SOURCE_LA);
        $paymentStore->setIsManual(true);

        if ($this->dueAt) {
            $paymentStore->setDueDate($this->dueAt);
        }

        $nextPaymentDate = (new Carbon($recurrence->last_payment))->addDays($recurrence->recurrence);

        $payments = $paymentStore->storePayments(
            $subscriber,
            $orderResult,
            $nextPaymentDate,
            $recurrence->order_number,
            $clientTaxTransaction,
            $installment,
        );

        $payment = $payments->first(); // recurrence has a single payment

        $payment->recurrences()->attach($recurrence);

        return $payment;
    }

    private function createFailedPaymentLog(
        $platformId,
        $subscriberId,
        $orderResponse
    ): void {
        GatewayTransaction::createFailedTransaction(
            $platformId,
            $subscriberId,
            $orderResponse ?? null,
            $this->origin ?? null
        );
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
        Payment $payment
    ): void {
        $this->markFailedPayment($payment); // mark only (payment status = 'failed')

        $paymentPlans = $payment->plans();
        $paymentPlans->updateExistingPivot($paymentPlans->allRelatedIds(), [
            'status' => PaymentPlan::STATUS_FAILED,
        ]);
    }

    private function markFailedPayment(Payment $payment): void
    {
        $payment->status = Payment::STATUS_FAILED;
        $payment->save();
    }

    protected function handleBoletoCreated($payment, $subscriber): void
    {
        $this->triggerBankSlipCreatedEvent($payment);

        $platform = $payment->platform;
        if ($platform->notifications_whatsapp ?? false) {
            Log::debug('whatsapp:boleto-created:publishing', ['payment_id' => $payment->id]);
            Whatsapp::boletoCreated($payment);
        }

        if ($this->skipEmail) {
            return;
        }

        $this->boletoOrder->sendBoletoMail($subscriber, $payment);
    }

    protected function handlePixCreated($payment, $subscriber): void
    {
        $this->triggerPixCreatedEvent($payment);

        $platform = $payment->platform;
        if ($platform->notifications_whatsapp ?? false) {
            Log::debug('whatsapp:pix-created:publishing', ['payment_id' => $payment->id]);
            Whatsapp::pixCreated($payment);
        }

        if ($this->skipEmail) {
            return;
        }

        $this->pixOrder->sendPixMail($subscriber, $payment);
    }

}
