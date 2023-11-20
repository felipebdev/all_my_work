<?php

namespace App\Utils;

use App\Payment;
use App\Subscriber;
use App\Transaction;
use Exception;
use Illuminate\Support\Facades\Log;
use Modules\Integration\Enums\EventEnum;
use Modules\Integration\Events\LeadData;
use Modules\Integration\Events\PaymentData;
use Modules\Integration\Events\TransactionData;
use Modules\Integration\Queue\Jobs\HandleIntegration;

trait TriggerIntegrationJob
{
    public function triggerPaymentApprovedEvent(Payment $payment)
    {
        try {
            HandleIntegration::dispatch(
                EventEnum::PAYMENT_APPROVED,
                $payment->platform_id,
                $payment->plans->map->only(['id'])->toArray(),
                new PaymentData($payment)
            );
        } catch (Exception $e) {
            $this->handleException(EventEnum::PAYMENT_APPROVED, $e, $payment);
        }
    }

    public function triggerPaymentRefundEvent(Payment $payment)
    {
        try {
            $overrideData = ['payment.status' => 'canceled'];
            HandleIntegration::dispatch(
                EventEnum::PAYMENT_REFUND,
                $payment->platform_id,
                $payment->plans->map->only(['id'])->toArray(),
                new PaymentData($payment, $overrideData)
            );
        } catch (Exception $e) {
            $this->handleException(EventEnum::PAYMENT_REFUND, $e, $payment);
        }
    }

    public function triggerPaymentChargebackEvent(Payment $payment)
    {
        try {
            HandleIntegration::dispatch(
                EventEnum::PAYMENT_CHARGEBACK,
                $payment->platform_id,
                $payment->plans->map->only(['id'])->toArray(),
                new PaymentData($payment)
            );
        } catch (Exception $e) {
            $this->handleException(EventEnum::PAYMENT_CHARGEBACK, $e, $payment);
        }
    }

    public function triggerBankSlipCreatedEvent(Payment $payment)
    {
        try {
            HandleIntegration::dispatch(
                EventEnum::BANK_SLIP_CREATED,
                $payment->platform_id,
                $payment->plans->map->only(['id'])->toArray(),
                new PaymentData($payment)
            );
        } catch (Exception $e) {
            $this->handleException(EventEnum::BANK_SLIP_CREATED, $e, $payment);
        }
    }

    public function triggerLeadCreatedEvent(Subscriber $subscriber)
    {
        try {
            HandleIntegration::dispatch(
                EventEnum::LEAD_CREATED,
                $subscriber->platform_id,
                [$subscriber->plan_id],
                new LeadData($subscriber)
            );
        } catch (Exception $e) {
            $this->handleException(EventEnum::LEAD_CREATED, $e, $subscriber);
        }
    }

    public function triggerNeverAccessEvent(Subscriber $subscriber)
    {
        try {
            HandleIntegration::dispatch(
                EventEnum::NEVER_ACCESSED,
                $subscriber->platform_id,
                [$subscriber->plan_id],
                new LeadData($subscriber)
            );
        } catch (Exception $e) {
            $this->handleException(EventEnum::NEVER_ACCESSED, $e, $subscriber);
        }
    }

    public function triggerSubscriptionCanceledEvent(Payment $payment)
    {
        try {
            $overrideData = ['payment.status' => 'canceled'];
            HandleIntegration::dispatch(
                EventEnum::SUBSCRIPTION_CANCELED,
                $payment->platform_id,
                $payment->plans->map->only(['id'])->toArray(),
                new PaymentData($payment, $overrideData)
            );
        } catch (Exception $e) {
            $this->handleException(EventEnum::SUBSCRIPTION_CANCELED, $e, $payment);
        }
    }

    public function triggerTransactionRefusedEvent(Transaction $transaction)
    {
        try {
            HandleIntegration::dispatch(
                EventEnum::PAYMENT_REFUSED,
                $transaction->platform_id,
                $transaction->plans->map->only(['id'])->toArray(),
                new TransactionData($transaction)
            );
        } catch (Exception $e) {
            $this->handleException(EventEnum::PAYMENT_REFUSED, $e, $transaction);
        }
    }

    // public function triggerPaymentExpiredEvent(Payment $payment)
    // {
    //     try {
    //         HandleIntegration::dispatch(
    //             EventEnum::PAYMENT_EXPIRED,
    //             $payment->platform_id,
    //             $payment->plans->map->only(['id'])->toArray(),
    //             new PaymentData($payment)
    //         );
    //     } catch (Exception $e) {}
    // }

    public function handleException(string $eventType, Exception $e, $payload)
    {
        $message = "[$eventType]: {$e->getMessage()}";
        Log::error($message, ['payload' => $payload]);
    }

}
