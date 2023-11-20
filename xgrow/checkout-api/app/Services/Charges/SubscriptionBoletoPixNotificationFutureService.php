<?php

namespace App\Services\Charges;

use App\Constants\LogKeys;
use App\Exceptions\NotImplementedException;
use App\Jobs\BoletoPixFutureNotificationJob;
use App\Logs\ChargeLog;
use App\Mail\BoletoPix\SendMailUpcomingBoletoPix;
use App\Recurrence;
use App\Services\Contracts\SubscriptionServiceInterface;
use App\Services\EmailTaggedService;
use App\Subscriber;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SubscriptionBoletoPixNotificationFutureService
{
    private SubscriptionServiceInterface $subscriptionService;

    public function __construct(SubscriptionServiceInterface $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    private array $types = [
        Recurrence::PAYMENT_METHOD_BOLETO,
        Recurrence::PAYMENT_METHOD_PIX
    ];

    /**
     * Dispatch notification about recurrence
     *
     * @param  \App\Recurrence  $recurrence  true if dispatched, false if not applicable
     * @return bool
     */
    public function dispatchSingleRecurrenceNotification(Recurrence $recurrence): bool
    {
        ChargeLog::includeRecurrenceContext($recurrence);

        Log::withContext(['email' => $recurrence->subscriber->email ?? '']);

        if (!$this->canNotifyRecurrence($recurrence)) {
            Log::info('Cant notify recurrence');
            return false;
        }

        BoletoPixFutureNotificationJob::dispatchSync($recurrence);

        return true;
    }

    public function handleEmailNotification(Recurrence $recurrence)
    {
        if (!$this->canNotifyRecurrence($recurrence)) {
            Log::info('Cant notify recurrence');
            return false;
        }

        $platformId = $recurrence->plan->platform_id;

        if (strlen(config('app.renewal_link')) > 0) {
            $mail = new SendMailUpcomingBoletoPix($recurrence);
            EmailTaggedService::mail($platformId, 'UPCOMING_BOLETO_PIX', $mail, [$recurrence->subscriber->email]);
        }

        $this->saveLastRecurrenceIdProcessedToday($recurrence->id);
    }


    /**
     * Check if recurrence satisfies conditions for notification
     *
     * @param  \App\Recurrence  $recurrence
     * @return bool true if satisfies conditions, false otherwise
     */
    private function canNotifyRecurrence(Recurrence $recurrence): bool
    {
        if ($recurrence->id <= $this->getLastRecurrenceIdProcessedToday()) {
            ChargeLog::info('Ops, recurrence already processed today');
            return false;
        }

        $recurrencePaymentMethod = $recurrence->payment_method;

        if (!in_array($recurrencePaymentMethod, $this->types)) {
            $message = "Recurrence notification not implemented to this payment method: {$recurrencePaymentMethod}";
            throw new NotImplementedException($message);
        }

        $subscriber = $recurrence->subscriber;
        if ($subscriber->status != Subscriber::STATUS_ACTIVE) {
            ChargeLog::info('Subscription charge unavailable: subscriber is not active');
            return false;
        }

        if ($recurrence->type != Recurrence::TYPE_SUBSCRIPTION) {
            ChargeLog::info('Subscription charge unavailable: recurrence is not a subscription');
            return false;
        }

        $plan = $recurrence->plan;

        $activeCharge = $plan->charge_until == 0 || $recurrence->current_charge < $plan->charge_until;

        if (!$activeCharge) {
            ChargeLog::info('Subscription charge unavailable: no active charge');
            return false;
        }

        $activeSubscription = $this->subscriptionService->hasActiveSubscription(
            $subscriber->id,
            $subscriber->platform_id,
            $plan->id
        );

        if (!$activeSubscription) {
            ChargeLog::info('Subscription charge unavailable: no active subscription');
            return false;
        }

        return true;
    }

    private function saveLastRecurrenceIdProcessedToday($recurrenceId): void
    {
        $ttlInSeconds = 60 * 60 * 23; // 23h

        Cache::put(LogKeys::CRON_NOTIFY_UPCOMING_BOLETO_PIX_LAST_RECURRENCE_ID_TODAY, $recurrenceId, $ttlInSeconds);
    }

    private function getLastRecurrenceIdProcessedToday(): int
    {
        return Cache::get(LogKeys::CRON_NOTIFY_UPCOMING_BOLETO_PIX_LAST_RECURRENCE_ID_TODAY, 0);
    }

}
