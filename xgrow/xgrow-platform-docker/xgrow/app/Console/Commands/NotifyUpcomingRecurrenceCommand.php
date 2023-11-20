<?php

namespace App\Console\Commands;

use App\Mail\SendMailUpcomingPaymentNoLimit;
use App\Mail\SendMailUpcomingRecurrenceSubscription;
use App\Payment;
use App\Recurrence;
use App\Services\EmailTaggedService;
use App\Subscriber;
use Carbon\CarbonImmutable;
use DateTimeInterface;
use Illuminate\Console\Command;

class NotifyUpcomingRecurrenceCommand extends Command
{
    protected $signature = 'xgrow:upcoming:recurrence'.
    '{--subscriber_id= : Restrict to single subscriber}'.
    '{--id= : Restrict to single recurrence_id (subscription)/payment_id (no-limit)}'.
    '{--days=15 : Specify number of days}'.
    '{--dry-run : Run in test mode (no real mail sending)}';

    protected $description = 'Notify user about upcoming charge of subscription/no-limit';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        if ($this->option('dry-run')) {
            echo 'Dry-run mode'. "\n";
        }

        $this->handleUpcomingSubscriptions();
        $this->handleUpcomingNoLimit();
    }

    private function handleUpcomingSubscriptions()
    {
        $days = (int) $this->option('days');

        $recurrences = $this->listRecurrenceSubscription($days);

        foreach ($recurrences as $recurrence) {
            $platformId = $recurrence->payments->first();
            $mail = new SendMailUpcomingRecurrenceSubscription($recurrence);

            if ($this->option('dry-run')) {
                $columns = ['id', 'type', 'order_number', 'last_payment', 'recurrence'];
                echo implode(';', $recurrence->only($columns)). "\n";
                continue;
            }
            EmailTaggedService::mail($platformId, 'UPCOMING_CHARGE', $mail);
        }
    }

    /**
     * List recurrences that will be charged in the next N days.
     *
     * @param  int  $days Number of days before charge date
     * @return mixed
     */
    private function listRecurrenceSubscription(int $days)
    {
        $subscriberId = $this->option('subscriber_id');
        $recurrenceId = $this->option('id');

        // last_payment + recurrence = next_payment
        // next_payment - 15 days = today -> payment is upcoming in 15 days from now
        $sqlCondition = 'last_payment + INTERVAL recurrence DAY - INTERVAL ? DAY = CURDATE()';

        return Recurrence::where('type', '=', Recurrence::TYPE_SUBSCRIPTION)
            ->whereHas('subscriber', function ($q) {
                $q->where('status', '=', Subscriber::STATUS_ACTIVE);
            })
            ->whereHas('plan', function ($q) {
                $q->where('status', '=', 'active');
            })
            ->whereRaw($sqlCondition, [$days])
            ->when($subscriberId, function ($query, $subscriberId) {
                $query->where('subscriber_id', $subscriberId);
            })
            ->when($recurrenceId, function ($query, $recurrenceId) {
                $query->where('id', $recurrenceId);
            })
            ->get();
    }

    private function handleUpcomingNoLimit()
    {
        $days = (int) $this->option('days');
        $date = CarbonImmutable::now()->addDays($days);

        $payments = $this->listPaymentsNoLimit($date->toDateString());

        foreach ($payments as $payment) {
            $platformId = $payment->platform_id;
            $mail = new SendMailUpcomingPaymentNoLimit($payment);
            if ($this->option('dry-run')) {
                $columns = ['id', 'type', 'order_number', 'payment_date'];
                echo implode(';', $payment->only($columns)) . "\n";
                continue;
            }
            EmailTaggedService::mail($platformId, 'UPCOMING_CHARGE', $mail, [$payment->subscriber->email]);
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
        $subscriberId = $this->option('subscriber_id');
        $recurrenceId = $this->option('id');

        return Payment::where('type', Payment::TYPE_UNLIMITED)
            ->where('status', '=', Payment::STATUS_PENDING)
            ->whereHas('subscription', function ($q) {
                $q->where('status', '=', 'active');
            })
            ->with(['platform', 'subscriber'])
            ->where('payment_date', '=', $paymentDate)
            ->when($subscriberId, function ($query, $subscriberId) {
                $query->where('subscriber_id', $subscriberId);
            })
            ->when($recurrenceId, function ($query, $recurrenceId) {
                $query->where('id', $recurrenceId);
            })
            ->get();
    }
}
