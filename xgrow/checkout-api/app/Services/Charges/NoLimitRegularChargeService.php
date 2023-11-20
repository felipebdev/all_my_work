<?php

namespace App\Services\Charges;

use App\Jobs\MundipaggUnlimitedOrder;
use App\Logs\ChargeLog;
use App\Payment;
use App\Services\Contracts\SubscriptionServiceInterface;
use App\Subscriber;
use App\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Str;

class NoLimitRegularChargeService
{
    private SubscriptionServiceInterface $subscriptionService;

    private bool $dryRun = false;
    private bool $skipEmail = false;
    private bool $updateDate = false;

    public function enableDryRun(bool $dryRun = true): self
    {
        $this->dryRun = $dryRun;
        return $this;
    }

    public function enableSkipEmail(bool $skipEmail = true): self
    {
        $this->skipEmail = $skipEmail;
        return $this;
    }

    public function enableUpdateDate(bool $updateDate = true): self
    {
        $this->updateDate = $updateDate;
        return $this;
    }

    public function __construct(SubscriptionServiceInterface $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
        $this->baseDate = Carbon::now();
    }

    public function dispatchSingleNoLimitPayment(Payment $payment)
    {
        ChargeLog::withContext(['payment-trace-id' => (string) Str::uuid()]);

        ChargeLog::includePaymentContext($payment);

        if (!$this->canChargePayment($payment)) {
            return false;
        }

        ChargeLog::info('Payment will be dispatched', ['payment_id' => $payment->id]);

        if ($this->dryRun) {
            return true;
        }

        MundipaggUnlimitedOrder::dispatch($payment, $this->skipEmail, $this->updateDate);

        return true;
    }

    public function canChargePayment($payment)
    {
        if (!$this->allSubscriptionsAreActive($payment)) {
            ChargeLog::info('No-limit charge unavailable: at least one subscription is not active');
            return false;
        }

        if ($payment->subscriber->status != Subscriber::STATUS_ACTIVE) {
            ChargeLog::info('No-limit charge unavailable: subscriber is not active');
            return false;
        }

        // check for existing transaction on same day to prevent duplicated charge
        $transactionToday = Transaction::query()
            ->where('payment_id', $payment->id)
            ->where('origin', Transaction::ORIGIN_REGULAR)
            ->whereRaw('DATE(created_at) = ?', [Carbon::now()->toDateString()])
            ->first();

        if ($transactionToday) {
            ChargeLog::info('No-Limit try ignored: transaction already exists on current day for this payment', [
                'transaction' => $transactionToday->toArray(),
            ]);

            return false;
        }

        return true;
    }

    private function allSubscriptionsAreActive(Payment $payment): bool
    {
        foreach ($payment->plans as $plan) {
            $isActive = $this->subscriptionService->hasActiveSubscription(
                $payment->subscriber->id,
                $payment->subscriber->platform_id,
                $plan->id
            );

            if (!$isActive) {
                return false;
            }
        }

        return true;
    }

}
