<?php

namespace App\Console\Commands;

use App\Jobs\MundipaggRecurrenceOrder;
use App\Recurrence;
use App\Subscriber;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Queue\QueueManager;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Usage example:
 *
 * php artisan xgrow:fix-recurrences-last-payment --log-only 5305a8a0-d993-427d-a803-bf5a1c1a2688  --expiring_date_initial=2022-11-01 --expiring_date_final=2022-11-30
 *
 */

class FixRecurrenceLastPayment extends Command
{
    protected $signature = 'xgrow:fix-recurrences-last-payment {platform_id} '.
    '{--expiring_date_initial= : Expiring date initial} '.
    '{--expiring_date_final= : Expiring date final} '.
    '{--subscriber_id= : Restrict to single subscriber} '.
    '{--recurrence_ids= : Restrict to recurrence IDs} '.
    '{--log-only : no real transaction, only logs} ';

    protected $description = 'Fix Recurrences Last Payment command';

    public function handle(QueueManager $queueManager)
    {
        $platformId = $this->argument('platform_id') ?? null;
        $subscriberId = $this->option('subscriber_id') ?? null;
        $expiringDateInitial = $this->option('expiring_date_initial') ?? null;
        $expiringDateFinal = $this->option('expiring_date_final') ?? null;
        $recurrenceIds = $this->option('recurrence_ids') ?? '';
        $logOnly = $this->option('log-only') ?? false;

        $recurrenceIdsArray = array_filter(explode(',', $recurrenceIds));

        $params = [
            'platform' => $platformId,
            'subscriber' => $subscriberId,
            'expiring_date_initial' => $expiringDateInitial,
            'expiring_date_final' => $expiringDateFinal,
            'recurrence_ids' => $recurrenceIds,
            'log-only' => $logOnly
        ];

        Log::withContext(['command_correlation_id' => (string) Str::uuid(), 'params' => $params]);
        Log::info(['Fix Recurrences Last Payment command']);

        Log::info('Fix Recurrences Last Payment command starting');

        $nextPaymentCondition = "DATE(recurrences.last_payment + INTERVAL recurrences.recurrence DAY) BETWEEN '".$expiringDateInitial."' and '".$expiringDateFinal."' ";

        $recurrences = Recurrence::
        selectRaw('
                recurrences.id AS id,
                recurrences.subscriber_id,
                recurrences.recurrence,
                recurrences.last_invoice AS last_invoice,
                recurrences.last_payment AS last_payment,
                recurrences.current_charge AS current_charge,
                recurrences.type,
                recurrences.payment_method,
                recurrences.total_charges AS total_charges,
                recurrences.plan_id,
                recurrences.order_number AS order_number,
                DATE_ADD(recurrences.last_payment,INTERVAL recurrences.recurrence DAY) as proximo_pagamento
            ')
            ->join('subscriptions', function ($query) {
                $query->on('subscriptions.subscriber_id', '=', 'recurrences.subscriber_id')
                    ->on('subscriptions.plan_id', '=', 'recurrences.plan_id');
            })
            ->leftJoin('subscribers', 'subscribers.id', '=', 'recurrences.subscriber_id')
            ->leftJoin('plans', 'plans.id', '=', 'recurrences.plan_id')
            ->where('subscribers.status', Subscriber::STATUS_ACTIVE)
            ->where('recurrences.payment_method', Recurrence::PAYMENT_METHOD_CREDIT_CARD)
            ->whereRaw(
                "(DATE(recurrences.last_payment + INTERVAL recurrences.recurrence DAY) >= ? AND DATE(recurrences.last_payment + INTERVAL recurrences.recurrence DAY) <= ?)",
                [
                    $expiringDateInitial ." 00:00:00",
                    $expiringDateFinal ." 23:59:59"
                ]
            )
            ->where(function ($q) {
                $q->where('plans.charge_until', '=', 0)
                    ->orWhereRaw('recurrences.current_charge < plans.charge_until');
            })
            ->when($platformId, function ($query, $platformId) {
                $query->where('subscriptions.platform_id', $platformId);
            })
            ->when($subscriberId, function ($query, $subscriberId) {
                $query->where('subscribers.id', $subscriberId);
            })
            ->whereNull('subscriptions.canceled_at')
            ->whereNull('subscriptions.payment_pendent')
            ->whereNotNull('subscriptions.order_number') // order_number required
            ->when($recurrenceIdsArray, function ($query, $recurrenceIdsArray) {
                $query->whereIn('recurrences.id', $recurrenceIdsArray);
            });

        $sql = $recurrences->toSql();

        $recurrences = $recurrences->get();

        if ($recurrences->count() > 0) {
            $fixed = 0;
            foreach ($recurrences as $recurrence) {
                $nextDate = Carbon::now()->subDay($recurrence->recurrence)->toDateString();

                if ( !$logOnly ) {
                    $recurrence->update(
                        ['last_payment' => $nextDate]
                    );

                    MundipaggRecurrenceOrder::dispatch($recurrence);
                }

                Log::info('Recurrences fixed to last payment'.$nextDate.': ', ['recurrence' => $recurrence]);
                $fixed ++;
            }

            Log::info('Recurrences fixed: '.$fixed);
        }
        
        Log::info('Recurrences SQL: ', ['sql' => $sql]);
        Log::info('Fix Recurrences Last Payment command finished');

        Log::withoutContext();
    }
}
