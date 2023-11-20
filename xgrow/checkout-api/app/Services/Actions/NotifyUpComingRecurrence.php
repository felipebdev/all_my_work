<?php

namespace App\Services\Actions;

use App\Constants\LogKeys;
use App\Mail\SendMailUpcomingPaymentNoLimit;
use App\Mail\SendMailUpcomingRecurrenceSubscription;
use App\Payment;
use App\Recurrence;
use App\Services\EmailTaggedService;
use App\Subscriber;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class NotifyUpComingRecurrence
{

    private $dryRun;
    private $days;
    private $subscriberId;
    private $recurrenceId;

    public function __construct($dryRun, $days, $subscriberId, $recurrenceId){
        $this->dryRun = $dryRun;
        $this->days = $days;
        $this->subscriberId = $subscriberId;
        $this->recurrenceId = $recurrenceId;
    }

    public function __invoke(){
        if ($this->dryRun) {
            Log::info('Dry-run mode');
        }
        $this->handleUpcomingSubscriptions();
        $this->handleUpcomingNoLimit();
    }


    private function handleUpcomingSubscriptions()
    {

        $recurrences = $this->listRecurrenceSubscription($this->days);

        foreach ($recurrences as $recurrence) {
            $platformId = $recurrence->payments->first();
            $mail = new SendMailUpcomingRecurrenceSubscription($recurrence);

            if ($this->dryRun) {
                $columns = ['id', 'type', 'order_number', 'last_payment', 'recurrence'];
                echo implode(';', $recurrence->only($columns))."\n";
                continue;
            }

            EmailTaggedService::mail($platformId, 'UPCOMING_CHARGE', $mail);

            $this->saveLastSubscriptionProcessedToday($recurrence->id);
        }
    }

    /**
     * List recurrences that will be charged in the next N days.
     *
     * @param  int  $days  Number of days before charge date
     * @return mixed
     */
    private function listRecurrenceSubscription(int $days)
    {

        // last_payment + recurrence = next_payment
        // next_payment - 15 days = today -> payment is upcoming in 15 days from now
        $sqlCondition = 'DATE(last_payment + INTERVAL recurrence DAY - INTERVAL ? DAY) = ?';

        return Recurrence::where('type', '=', Recurrence::TYPE_SUBSCRIPTION)
            ->whereHas('subscriber', function ($q) {
                $q->where('status', '=', Subscriber::STATUS_ACTIVE);
            })
            ->whereHas('plan', function ($q) {
                $q->where('status', '=', 1);
            })
            ->whereRaw($sqlCondition, [$days, Carbon::now()->toDateString()])
            ->where('recurrences.id', '>', $this->getLastSubscriptionProcessedToday())
            ->when($this->subscriberId, function ($query, $subscriberId) {
                $query->where('subscriber_id', $subscriberId);
            })
            ->when($this->recurrenceId, function ($query, $recurrenceId) {
                $query->where('id', $recurrenceId);
            })
            ->orderBy('recurrences.id')
            ->get();
    }

    private function handleUpcomingNoLimit()
    {
        $date = Carbon::now()->addDays($this->days);

        $payments = $this->listPaymentsNoLimit($date->toDateString());

        foreach ($payments as $payment) {
            $platformId = $payment->platform_id;
            $mail = new SendMailUpcomingPaymentNoLimit($payment);
            if ($this->dryRun) {
                $columns = ['id', 'type', 'order_number', 'payment_date'];
                echo implode(';', $payment->only($columns))."\n";
                continue;
            }
            EmailTaggedService::mail($platformId, 'UPCOMING_CHARGE', $mail, [$payment->subscriber->email]);

            $this->saveLastNoLimitProcessedToday($payment->id);
        }
    }

    /**
     * List all payments that will be executed in a given date
     *
     * @param  string  $paymentDate  Y-m-d format
     * @return array|\Illuminate\Database\Concerns\BuildsQueries[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    private function listPaymentsNoLimit(string $paymentDate)
    {

        return Payment::where('type', Payment::TYPE_UNLIMITED)
            ->where('status', '=', Payment::STATUS_PENDING)
            ->whereHas('subscription', function ($q) {
                $q->where('status', '=', 'active');
            })
            ->with(['platform', 'subscriber'])
            ->where('payment_date', '=', $paymentDate)
            ->where('payments.id', '>', $this->getLastNoLimitProcessedToday())
            ->when($this->subscriberId, function ($query, $subscriberId) {
                $query->where('subscriber_id', $subscriberId);
            })
            ->when($this->recurrenceId, function ($query, $recurrenceId) {
                $query->where('id', $recurrenceId);
            })
            ->orderBy('payments.id')
            ->get();
    }

    private function saveLastSubscriptionProcessedToday($recurrenceId): void
    {
        $ttlInSeconds = 60 * 60 * 23; // 23h

        Cache::put(LogKeys::CRON_NOTIFY_UPCOMING_SUBSCRIPTION_LAST_RECURRENCE_ID_TODAY, $recurrenceId, $ttlInSeconds);
    }

    private function getLastSubscriptionProcessedToday(): int
    {
        return Cache::get(LogKeys::CRON_NOTIFY_UPCOMING_SUBSCRIPTION_LAST_RECURRENCE_ID_TODAY, 0);
    }

    private function saveLastNoLimitProcessedToday($paymentId): void
    {
        $ttlInSeconds = 60 * 60 * 23; // 23h

        Cache::put(LogKeys::CRON_NOTIFY_UPCOMING_NOLIMIT_LAST_PAYMENT_ID_TODAY, $paymentId, $ttlInSeconds);
    }

    private function getLastNoLimitProcessedToday(): int
    {
        return Cache::get(LogKeys::CRON_NOTIFY_UPCOMING_NOLIMIT_LAST_PAYMENT_ID_TODAY, 0);
    }

}
