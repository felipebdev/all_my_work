<?php

namespace App\Services\Charges;

use App\Jobs\MundipaggRecurrenceOrder;
use App\Jobs\MundipaggRecurrenceOrderRetry;
use App\Logs\ChargeLog;
use App\Payment;
use App\Recurrence;
use App\Services\Contracts\SubscriptionServiceInterface;
use App\Subscriber;
use App\Subscription;
use Carbon\Carbon;
use Illuminate\Contracts\Mail\Mailable as MailableContract;

class SubscriptionChargeService
{
    private $subscriptionService;

    public function __construct(SubscriptionServiceInterface $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Dispatch recurrence
     *
     * @param  \App\Recurrence  $recurrence  true if dispatched, false if not applicable
     * @return bool
     */
    public function dispatchSingleRecurrence(Recurrence $recurrence): bool
    {
        if (!$this->shouldChargeRecurrence($recurrence)) {
            return false;
        }

        $diff_in_days = 0;
        if (!empty($recurrence->last_payment)) {
            $to = Carbon::createFromFormat('Y-m-d H:s:i', date('Y-m-d H:s:i'));
            $from = Carbon::createFromFormat('Y-m-d H:s:i', $recurrence->last_payment);
            //check charge date
            $diff_in_days = $to->diffInDays($from);
        }

        if ($diff_in_days == (int) $recurrence->recurrence || empty($recurrence->last_payment)) {
            ChargeLog::info('Recurrence > ', $recurrence->toArray());

            //dispatch order
            MundipaggRecurrenceOrder::dispatch($recurrence);
            return true;
        }

        return false;
    }

    /**
     * Retry charge of a failed payment
     *
     * The payment must be the "original failed payment"
     *
     * @param  \App\Payment  $payment
     * @param  \Illuminate\Contracts\Mail\Mailable  $mailOnFail  Mail to send if user's transaction fails
     * @param  bool  $cancelSubscriptionOnFail  Cancel subscription if  user's transaction fails (default false)
     * @param  bool  $dryRun  Don't execute a real transaction (default false)
     * @return bool true if retry was dispatched, false if not satisfies conditions
     */
    public function retryChargePayment(
        Payment $payment,
        MailableContract $mailOnFail,
        bool $cancelSubscriptionOnFail = false,
        bool $dryRun = false,
        ?string $baseDate = null
    ): bool {
        if (!is_null($payment->payment_id)) {
            ChargeLog::info('Subscription retry ignored: given payment is a retry', [
                'original_payment_id' => $payment->id ?? null,
                'related_payment_id' => $payment->payment_id ?? null,
            ]);
            return false;
        }

        $recurrence = $payment->recurrences()->first();

        if (!$this->shouldRetryChargeRecurrence($recurrence)) {
            return false;
        }

        Payment::where('id', $payment->id)->increment('attempts');

        ChargeLog::info('Subscription retry being dispatched', [
            'payment_id' => $payment->id ?? null,
            'recurrence_id' => $recurrence->id ?? null
        ]);

        MundipaggRecurrenceOrderRetry::dispatch(
            $recurrence,
            $payment,
            $mailOnFail,
            $cancelSubscriptionOnFail,
            $dryRun,
            $baseDate
        );
        return true;
    }

    /**
     * Check if recurrence satisfies conditions for charging on "due date"
     *
     * @param  \App\Recurrence  $recurrence
     * @return bool true if satisfies conditions, false otherwise
     */
    private function shouldChargeRecurrence(Recurrence $recurrence): bool
    {
        $subscriber = $recurrence->subscriber;
        if ($subscriber->status != Subscriber::STATUS_ACTIVE) {
            ChargeLog::info('Subscription charge ignored: subscriber is not active', [
                'recurrence_id' => $recurrence->id ?? null,
            ]);
            return false;
        }

        if ($recurrence->type != Recurrence::TYPE_SUBSCRIPTION) {
            ChargeLog::info('Subscription charge ignored: recurrence is not a subscription', [
                'recurrence_id' => $recurrence->id ?? null,
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

        if (!$activeCharge) {
            ChargeLog::info('Subscription charge ignored: no active charge',
                ['recurrence_id' => $recurrence->id ?? null]);
            return false;
        }

        if (!$activeSubscription) {
            ChargeLog::info('Subscription charge ignored: no active subscription',
                ['recurrence_id' => $recurrence->id ?? null]);
            return false;
        }

        return true;
    }

    /**
     * Check if a recurrence satisfies conditions to execute a "retry"
     *
     * @param  \App\Recurrence  $recurrence
     * @return bool true if satisfies conditions, false otherwise
     */
    private function shouldRetryChargeRecurrence(Recurrence $recurrence): bool
    {
        $subscriber = $recurrence->subscriber;
        if ($subscriber->status != Subscriber::STATUS_ACTIVE) {
            ChargeLog::info('Subscription retry ignored: subscriber is not active', [
                'recurrence_id' => $recurrence->id ?? null,
                'subscriber_id' => $subscriber->id ?? null,
            ]);
            return false;
        }

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

        if (!$activeCharge) {
            ChargeLog::info('Subscription retry ignored: no active charge', [
                'recurrence_id' => $recurrence->id ?? null,
                'subscriber_id' => $subscriber->id ?? null,
            ]);
            return false;
        }

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

        if ($subscription->status != Subscription::STATUS_FAILED) {
            ChargeLog::info('Subscription retry ignored: subscription is not failed', [
                'recurrence_id' => $recurrence->id ?? null,
                'subscriber_id' => $subscriber->id ?? null,
            ]);
            return false;
        }

        return true;
    }

}
