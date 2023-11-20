<?php

namespace App\Services\Actions;

use App\Constants\LogKeys;
use App\Logs\ChargeLog;
use App\Recurrence;
use App\Subscriber;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

/**
 * Process subscriptions with order number
 */
class RunRegularChargeForSubscriptionAction extends BaseRegularChargeForSubscription
{

    public function __invoke(): int
    {
        $this->logBegin();

        Redis::set(LogKeys::CHARGE_REGULAR_SUBSCRIPTION_AFFECTED, 0);

        $nextPaymentCondition = 'DATE(recurrences.last_payment + INTERVAL recurrences.recurrence DAY) = ?';

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
                recurrences.order_number AS order_number
            ')
            ->join('subscriptions', function ($query) {
                $query->on('subscriptions.subscriber_id', '=', 'recurrences.subscriber_id')
                    ->on('subscriptions.plan_id', '=', 'recurrences.plan_id');
            })
            ->leftJoin('subscribers', 'subscribers.id', '=', 'recurrences.subscriber_id')
            ->leftJoin('plans', 'plans.id', '=', 'recurrences.plan_id')
            ->where('subscribers.status', Subscriber::STATUS_ACTIVE)
            ->where('recurrences.payment_method', Recurrence::PAYMENT_METHOD_CREDIT_CARD)
            ->whereRaw($nextPaymentCondition, [Carbon::now()->toDateString()])
            ->where(function ($q) {
                $q->where('plans.charge_until', '=', 0)
                    ->orWhere('recurrences.current_charge', '<', 'plans.charge_until');
            })
            ->when($this->platformId, function ($query, $platformId) {
                $query->where('subscriptions.platform_id', $platformId);
            })
            ->when($this->subscriberId, function ($query, $subscriberId) {
                $query->where('subscribers.id', $subscriberId);
            })
            ->when($this->recurrenceId, function ($query, $recurrenceId) {
                $query->where('recurrences.id', $recurrenceId);
            })
            ->whereNull('subscriptions.canceled_at')
            ->whereNull('subscriptions.payment_pendent')
            ->whereNotNull('subscriptions.order_number') // order_number required
            ->groupBy('subscriptions.id')
            ->get();

        Log::info(LogKeys::CHARGE_REGULAR_SUBSCRIPTION_FOUND, ['value' => $recurrences->count()]);

        $total = 0;
        foreach ($recurrences as $recurrence) {
            $result = $this->subscriptionChargeService->dispatchSingleRecurrence($recurrence);
            if ($result) {
                $total++;
                Redis::incr(LogKeys::CHARGE_REGULAR_SUBSCRIPTION_AFFECTED);
            }
        }

        $this->logEnd($total);

        return $total;
    }

    private function logBegin(): void
    {
        $uuid = (string) Str::uuid();
        Log::withContext(['schedule-trace-id' => $uuid]);
        Log::withContext(['running_origin' => 'charge_subscription']);
        Log::withContext(['hostname-dispatcher' => gethostname()]);

        Log::info(LogKeys::CHARGE_REGULAR_SUBSCRIPTION_LASTSTART, ['value' => Carbon::now()->toDateTimeString()]);

        Redis::set(LogKeys::CHARGE_REGULAR_SUBSCRIPTION_LASTSTART, Carbon::now()->toDateTimeString());
        Redis::set(LogKeys::CHARGE_REGULAR_SUBSCRIPTION_LASTTRACE, $uuid);

        // @todo Delete on ChargeLog deprecation
        ChargeLog::withContext(['schedule-trace-id' => $uuid]);
    }

    private function logEnd(int $total): void
    {
        Log::info(LogKeys::CHARGE_REGULAR_SUBSCRIPTION_TOTAL, ['value' => $total]);
        Log::info(LogKeys::CHARGE_REGULAR_SUBSCRIPTION_LASTEND, ['value' => Carbon::now()->toDateTimeString()]);

        Redis::set(LogKeys::CHARGE_REGULAR_SUBSCRIPTION_TOTAL, $total);
        Redis::set(LogKeys::CHARGE_REGULAR_SUBSCRIPTION_LASTEND, Carbon::now()->toDateTimeString());

        Log::withoutContext();
    }

}
