<?php

namespace App\Services\Charges;

use App\Jobs\MundipaggRecurrenceOrderRetry;
use App\Logs\ChargeLog;
use App\Payment;
use App\Recurrence;
use App\Services\ChargeRulerSettings;
use App\Services\Contracts\SubscriptionServiceInterface;
use App\Subscriber;
use App\Subscription;
use App\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SubscriptionRetryChargeService
{

    private bool $dryRunDebug = false;

    private ?string $baseDateDebug = null;

    private bool $skipEmail = false;

    private SubscriptionServiceInterface $subscriptionService;

    public function __construct(SubscriptionServiceInterface $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    public function enableDryRunDebug(bool $mode = true): self
    {
        $this->dryRunDebug = $mode;
        return $this;
    }

    public function setBaseDateDebug(?string $date = null): self
    {
        $this->baseDateDebug = $date;
        return $this;
    }

    public function skipEmail(bool $skipEmail = true): self
    {
        $this->skipEmail = $skipEmail;
        return $this;
    }

    public function retryChargePayment(Payment $payment, int $mailId): bool
    {
        ChargeLog::withContext(['charge-trace-id' => (string) Str::uuid()]);

        //if (!is_null($payment->payment_id)) {
        //    ChargeLog::info('Subscription retry ignored: given payment is a retry', [
        //        'original_payment_id' => $payment->id ?? null,
        //        'related_payment_id' => $payment->payment_id ?? null,
        //    ]);
        //    return false;
        //}

        $recurrence = $payment->recurrences()->first();

        if (!$this->shouldRetryChargeSubscription($payment)) {
            return false;
        }

        if ($this->dryRunDebug) {
            $this->writeCsvDebugFile($recurrence, $payment);
            return true; // fake retry
        }

        $payment->increment('attempts');

        ChargeLog::info('Subscription retry being dispatched', [
            'payment_id' => $payment->id ?? null,
            'recurrence_id' => $recurrence->id ?? null
        ]);

        // skip non-canceling email
        $shouldSkipMail = ChargeRulerSettings::isCancelRequired($mailId)
            ? $this->skipEmail
            : true;

        MundipaggRecurrenceOrderRetry::dispatch($recurrence, $payment, $mailId, $shouldSkipMail);
        return true;
    }

    /**
     * Check if a Payment satisfies conditions to execute a "retry"
     *
     * @param  \App\Payment  $failedPayment
     * @return bool
     */
    public function shouldRetryChargeSubscription(Payment $failedPayment): bool
    {
        $recurrence = $failedPayment->recurrences()->first();

        // check if subscriber is active
        $subscriber = $recurrence->subscriber;
        if ($subscriber->status != Subscriber::STATUS_ACTIVE) {
            ChargeLog::info('Subscription retry ignored: subscriber is not active', [
                'recurrence_id' => $recurrence->id ?? null,
                'subscriber_id' => $subscriber->id ?? null,
            ]);
            return false;
        }

        // check if recurrence is really a subscription
        if ($recurrence->type != Recurrence::TYPE_SUBSCRIPTION) {
            ChargeLog::info('Subscription retry ignored: recurrence is not subscription', [
                'recurrence_id' => $recurrence->id ?? null,
                'subscriber_id' => $subscriber->id ?? null,
            ]);
            return false;
        }

        $plan = $recurrence->plan;

        $activeCharge = $plan->charge_until == 0 || $recurrence->current_charge < $plan->charge_until;

        $activeSubscription = $this->subscriptionService->hasActiveSubscription(
            $subscriber->id,
            $subscriber->platform_id,
            $plan->id
        );

        // check if has active charge
        if (!$activeCharge) {
            ChargeLog::info('Subscription retry ignored: no active charge', [
                'recurrence_id' => $recurrence->id ?? null,
                'subscriber_id' => $subscriber->id ?? null,
            ]);
            return false;
        }

        // check if has active subscription
        if (!$activeSubscription) {
            ChargeLog::info('Subscription retry ignored: no active subscription', [
                'recurrence_id' => $recurrence->id ?? null,
                'subscriber_id' => $subscriber->id ?? null,
            ]);
            return false;
        }

        $subscription = Subscription::where('subscriber_id', '=', $subscriber->id)
            ->where('platform_id', '=', $subscriber->platform_id)
            ->where('plan_id', '=', $plan->id)
            ->whereNull('canceled_at')
            ->first();

        // check if subscription is really in "failed" status
        if ($subscription->status != Subscription::STATUS_FAILED) {
            ChargeLog::info('Subscription retry ignored: subscription is not failed', [
                'recurrence_id' => $recurrence->id ?? null,
                'subscriber_id' => $subscriber->id ?? null,
            ]);
            return false;
        }

        // check for existing transaction on same day to prevent duplicated charge
        $transactionToday = Transaction::query()
            ->where('payment_id', $failedPayment->id)
            ->where('origin', Transaction::ORIGIN_RULER)
            ->whereRaw('DATE(created_at) = ?', [Carbon::now()->toDateString()])
            ->first();

        if ($transactionToday) {
            ChargeLog::info('Transaction already exists on current day for this payment', [
                'transaction' => $transactionToday->toArray(),
            ]);

            return false;
        }

        return true;
    }

    private function writeCsvDebugFile(Recurrence $recurrence, Payment $originalFailedPayment): void
    {
        $subscriber = $recurrence->subscriber;
        $platform = $subscriber->platform;
        $info = [
            Carbon::now()->toDateTimeString(),
            $this->baseDateDebug ?? Carbon::now()->toDateString(),
            $platform->name ?? '',
            $subscriber->name ?? '',
            $subscriber->email ?? '',
            $originalFailedPayment->id ?? '',
            $originalFailedPayment->order_code ?? '',
            $originalFailedPayment->payment_date ?? '',
        ];

        echo 'Writing to subscription-charge-log.csv: '.join(';', $info);
        Storage::disk('local')->append('subscription-charge-log.csv', join(';', $info));
    }
}
