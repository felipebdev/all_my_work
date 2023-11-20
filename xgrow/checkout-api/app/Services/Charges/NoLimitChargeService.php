<?php

namespace App\Services\Charges;

use App\Coupon;
use App\CreditCard;
use App\Exceptions\MissingImportantChargeDataException;
use App\Logs\ChargeLog;
use App\Mail\FactoryMail;
use App\Mail\Objects\MailPayload;
use App\Mail\SendMailRecurrencePaymentFailed;
use App\Mail\SendMailRecurrencePaymentSuccess;
use App\Payment;
use App\PaymentPlan;
use App\PaymentPlanSplit;
use App\Plan;
use App\Services\ChargeRulerSettings;
use App\Services\Contracts\SubscriptionServiceInterface;
use App\Services\EmailTaggedService;
use App\Services\Finances\Objects\Constants;
use App\Services\Finances\Objects\PriceTag;
use App\Services\Finances\Transaction\GatewayTransaction;
use App\Services\Mundipagg\MundipaggSplitService;
use App\Services\Mundipagg\SplitService;
use App\Services\MundipaggService;
use App\Transaction;
use App\Utils\TriggerIntegrationJob;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Mail\Mailable as MailableContract;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use MundiAPILib\APIException;
use MundiAPILib\Models\CreateCreditCardPaymentRequest;
use MundiAPILib\Models\CreateOrderItemRequest;
use MundiAPILib\Models\CreateOrderRequest;
use MundiAPILib\Models\CreatePaymentRequest;
use MundiAPILib\Models\GetOrderResponse;

class NoLimitChargeService
{
    use TriggerIntegrationJob;

    private $subscriptionService;

    private ?string $origin = null;

    private bool $skipEmail = false;

    private bool $updateDate = false;

    public static $forceFailStatusDebug = false; // WARNING: use only when debugging

    public function __construct(SubscriptionServiceInterface $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    public function setTransactionOrigin(string $origin): self
    {
        $this->origin = $origin;
        return $this;
    }

    public function skipEmail(bool $skipEmail = true): self
    {
        $this->skipEmail = $skipEmail;
        return $this;
    }

    public function enableUpdateDate(bool $updateDate = false): self
    {
        $this->updateDate = $updateDate;
        return $this;
    }

    /**
     * @param  \App\Payment  $payment
     * @param  int|null  $mailIdOnFail  Send specific email if charge fails
     */
    public function createPaymentOrder(Payment $payment, ?int $mailIdOnFail = null): void
    {
        ChargeLog::withContext(['order-trace-id' => (string) Str::uuid()]);
        ChargeLog::includePaymentContext($payment);
        ChargeLog::info('Processing of order started');
        $context = ChargeLog::getContext();

        $subscriber = $payment->subscriber;
        $platform = $payment->platform;
        $totalInstallments = $payment->installments;

        $customerId = $payment->customer_id;

        if (!$customerId) {
            if (app()->environment('release')) {
                return; // ignore on release
            }

            throw new MissingImportantChargeDataException("Subscriber {$subscriber->id} without card");
        }

        try {
            $orderRequest = new CreateOrderRequest();
            $orderRequest->closed = true;
            $orderRequest->customerId = $customerId;

            $metadata['obs'] = "Venda sem limite (parcela {$payment->installment_number} de {$totalInstallments})";
            $metadata['unlimited_sale'] = true;
            $metadata['total_installments'] = $totalInstallments;
            // tracing
            $metadata['payment-trace-id'] = $context['payment-trace-id'] ?? '';
            $metadata['order-trace-id'] = $context['order-trace-id'] ?? '';

            $metadata['payment_id'] = $payment->id ?? '';
            $metadata['payment_date'] = $payment->payment_date ?? '';
            $metadata['payment_order_number'] = $payment->order_number ?? '';
            $metadata['origin'] = $this->origin ?? Transaction::ORIGIN_TRANSACTION;
            $metadata['hostname-dispatcher'] = $context['hostname-dispatcher'] ?? '';
            $metadata['hostname-runner'] = gethostname();

            if (strlen($payment->coupon_id)) {
                try {
                    $coupon = Coupon::findOrFail($payment->coupon_id);
                    $metadata['cupom_id'] = $coupon->id;
                    $metadata['cupom_code'] = $coupon->code;
                } catch (Exception $e) {
                }
            }

            $items = [];
            foreach ($payment->plans as $plan) {
                $planAmount = str_replace('.', '', $plan->getPrice());

                $itemPrice = new CreateOrderItemRequest();
                $itemPrice->description = $plan->name;
                $itemPrice->quantity = 1;
                $itemPrice->amount = $planAmount;
                $itemPrice->code = $plan->id;
                $items[] = $itemPrice;
            }
            $orderRequest->items = $items;

            $mainPriceTag = null;
            $orderBumpPriceTags = [];
            $orderbumpId = null;
            foreach ($payment->plans as $plan) {
                if (($plan->pivot->type == 'order_bump') ||
                    (!empty($orderbumpId) && $orderbumpId == $plan->id)
                ) {
                    $metadata['order_bump_plan_id'] = $plan->id;
                    $metadata['order_bump_plan'] = $plan->name;
                    $metadata['order_bump_value'] = $plan->price;

                    $orderBumpPriceTags[] = PriceTag::fromDecimal($plan->id, $plan->getPrice());
                } else {
                    $metadata['plan_id'] = $plan->id;
                    $metadata['plan'] = $plan->name;
                    $metadata['value'] = $plan->price;

                    $mainPriceTag = PriceTag::fromDecimal($plan->id, $plan->getPrice());
                }

                $orderbumpId = $plan->order_bump_plan_id;
            }
            $orderRequest->metadata = $metadata;

            $splitService = new SplitService($platform->id);
            $splitResult = $splitService->getPrecalculatedPlanSplit($payment, $metadata);

            $paymentRequest = new CreatePaymentRequest();
            $paymentRequest->paymentMethod = "credit_card";
            $paymentRequest->amount = $splitResult->getTotalAmount();
            $paymentRequest->creditCard = new CreateCreditCardPaymentRequest();
            $paymentRequest->creditCard->cardId = CreditCard::findOrFail($payment->subscriber->credit_card_id)->card_id;

            $mundipaggSplitService = new MundipaggSplitService($platform->id);

            $paymentRequest->split = $mundipaggSplitService->generateMundipaggSplit($splitResult);
            $paymentRequest->metadata = $splitResult->getMetadata();
            $orderRequest->payments = [$paymentRequest];

            ChargeLog::info('Sending request to gateway', ['orderRequest' => $orderRequest]);

            $mundipaggService = new MundipaggService();
            $orderResponse = $mundipaggService->createClientOrder($orderRequest);

            if (self::$forceFailStatusDebug) {
                $orderResponse->status = Constants::MUNDIPAGG_FAILED;
            }

            ChargeLog::info('No Limit charge/retry: Mundipagg response', ['orderResponse' => $orderResponse]);

            $payment->status = $orderResponse->status;
            $payment->order_id = $orderResponse->id;
            $customerId = $orderResponse->customer->id;
            $payment->order_code = $orderResponse->code;

            if ($this->updateDate) {
                $payment->payment_date = Carbon::now()->toDateString();
            }

            if ($orderResponse->status == Constants::MUNDIPAGG_PAID) {
                ChargeLog::info('No Limit charge/retry: successful', ['successful_payment' => true]);

                foreach ($orderResponse->charges as $charge) {
                    $payment->charge_id = $charge->id;
                    $payment->charge_code = $charge->code;
                    $payment->confirmed_at = Carbon::now();

                    $splits = $charge->lastTransaction->split ?? [];

                    foreach ($splits as $split) {
                        $value = $split->amount / 100;
                        if ($split->options->chargeProcessingFee) {
                            $payment->service_value = $value; //Xgrow
                        } else {
                            $payment->customer_value = $value; //Customer
                        }
                    }

                    // update payment_plan_split
                    $paymentPlans = PaymentPlan::query()
                        ->where('payment_id', $payment->id)
                        ->get();

                    $paymentPlanIds = $paymentPlans->pluck('id');
                    $affected = PaymentPlanSplit::query()
                        ->whereIn('payment_plan_id', $paymentPlanIds)
                        ->update([
                            'order_code' => $orderResponse->code,
                        ]);
                }
                $this->handleSuccessPayment($payment, $orderResponse);

                $paymentPlans = $payment->plans();
                $paymentPlans->updateExistingPivot($paymentPlans->allRelatedIds(), [
                    'status' => PaymentPlan::STATUS_PAID,
                ]);
            } else {
                $failures = GatewayTransaction::getOrderFailures($orderResponse);
                ChargeLog::info('No Limit charge/retry: failed  (User error)', [
                    'failures' => $failures,
                    'successful_payment' => false,
                ]);

                $this->createFailedPaymentLog($platform->id, $subscriber->id, $orderResponse, $payment);

                $this->handleFailedPayment($payment, $mailIdOnFail);

                $paymentPlans = $payment->plans();
                $paymentPlans->updateExistingPivot($paymentPlans->allRelatedIds(), [
                    'status' => PaymentPlan::STATUS_FAILED,
                ]);
            }

            $payment->save();

            ChargeLog::info('Processing of payment ended');
        } catch (\RuntimeException $e) {
            ChargeLog::info('No Limit charge/retry: skipped (payment_plan_split error)', [
                'successful_payment' => false
            ]);
        } catch (APIException $e) {
            ChargeLog::info('No Limit charge/retry: failed (API error)', ['successful_payment' => false]);

            $this->createFailedPaymentLog($platform->id, $subscriber->id, $orderResponse ?? null, $payment);

            $this->handleFailedPayment($payment, $mailIdOnFail);

            Log::error(json_encode($e->getResponseBody()));
        } catch (Exception $e) {
            if (app()->bound('sentry')) {
                // Some unknown error occurred, capture and report it to Sentry for further investigation
                app('sentry')->captureException($e);
            }

            ChargeLog::alert('No Limit charge/retry: failed (Exception error)', [
                'successful_payment' => false,
                'exception' => $e
            ]);
        }

        ChargeLog::info('Processing of order ended');
    }

    private function handleSuccessPayment(Payment $payment, GetOrderResponse $response)
    {
        $platformId = $payment->platform_id;
        $subscriber = $payment->subscriber;

        GatewayTransaction::createSuccessfulTransaction($platformId, $subscriber->id, $response);

        $this->subscriptionService->enableSubscriptionByPayment($payment);

        if (!$this->skipEmail) {
            $mail = new SendMailRecurrencePaymentSuccess($platformId, $subscriber, $payment);

            EmailTaggedService::mail($platformId, 'CHARGE_RULER', $mail);
        }

        $this->triggerPaymentApprovedEvent($payment);
    }

    /**
     * Handle failed payment
     *
     * @param  \App\Payment  $payment
     * @param  int|null  $mailIdOnFail
     */
    private function handleFailedPayment(
        Payment $payment,
        ?int $mailIdOnFail = null
    ) {
        $platformId = $payment->platform_id;
        $subscriber = $payment->subscriber;

        ChargeLog::withContext(['subscriber_id' => $subscriber->id ?? null]);

        //$shouldCancel = ChargeRulerSettings::isCancelRequired($mailIdOnFail);
        //
        //if ($shouldCancel) {
        //    $this->cancelPaymentsAndSubscriptions($payment, $platformId, $mailIdOnFail);
        //} else {
        //    $this->markPaymentAndSubscriptions($payment, $subscriber);
        //}

        $this->markPaymentAndSubscriptions($payment, $subscriber);
    }

    /**
     * Mark payment as failed and all related products with failed payment
     *
     * @param  \App\Payment  $payment
     * @param $subscriber
     */
    private function markPaymentAndSubscriptions(Payment $payment, $subscriber): void
    {
        $this->markFailedPayment($payment); // mark only

        foreach ($payment->plans as $plan) {
            ChargeLog::info('No Limit charge/retry: marking as failed', [
                'plan_id' => $plan->id ?? null,
            ]);

            $this->subscriptionService->markSubscriptionWithFailedPayment($subscriber, $plan);
        }
    }

    /**
     * Mark Payment as failed
     *
     * @param  \App\Payment  $payment
     */
    private function markFailedPayment(Payment $payment): void
    {
        $payment->status = Payment::STATUS_FAILED;
        $payment->save();
    }

    private function cancelPaymentsAndSubscriptions(Payment $payment, $platformId, ?int $mailIdOnFail): void
    {
        ChargeLog::info('No Limit charge/retry: subscription and payments cancellation');

        $cancellationReason = 'Cancelado automaticamente pela régua de cobrança';

        $this->subscriptionService->cancelUnpaidPayments($payment->order_number, $cancellationReason);

        foreach ($payment->plans as $plan) {
            $this->subscriptionService->markSubscriptionWithFailedPayment($payment->subscriber, $plan);

            //$this->sendFailedPaymentEmail($platformId, $plan, $payment, $mailIdOnFail);
        }
    }

    private function sendFailedPaymentEmail(string $platformId, Plan $plan, Payment $payment, int $mailIdOnFail ): void
    {
        ChargeLog::withContext(['plan_id' => $plan->id ?? null]);
        ChargeLog::withContext(['mail_id' => $mailIdOnFail ?? null]);

        if ($this->skipEmail) {
            ChargeLog::info('No Limit charge/retry: failed charge, email notification skipped');
            return;
        }

        ChargeLog::info('No Limit charge/retry: failed charge, email notification');

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

}
