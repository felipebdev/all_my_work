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
 * Process subscriptions WITHOUT order_number
 *
 * @see \App\Services\Actions\RunRegularChargeForSubscriptionAction for version WITH order_number
 */
class RunRegularChargeForLegacySubscriptionAction extends BaseRegularChargeForSubscription
{

    public function __invoke(): int
    {
        $this->logBegin();

        Redis::set(LogKeys::CHARGE_REGULAR_LEGACY_AFFECTED, 0);

        $recurrences = Recurrence::
        selectRaw('
                DISTINCT MAX(recurrences.id) AS id,
                recurrences.subscriber_id,
                recurrences.recurrence,
                MAX(recurrences.last_invoice) AS last_invoice,
                MAX(recurrences.last_payment) AS last_payment,
                MAX(recurrences.current_charge) AS current_charge,
                recurrences.type,
                recurrences.payment_method,
                MAX(recurrences.total_charges) AS total_charges,
                recurrences.plan_id,
                MAX(recurrences.order_number) AS order_number
            ')
            ->leftJoin('subscriptions', function ($query) {
                $query->on('subscriptions.subscriber_id', '=', 'recurrences.subscriber_id')
                    ->on('subscriptions.plan_id', '=', 'recurrences.plan_id');
            })
            ->leftJoin('subscribers', 'subscribers.id', '=', 'recurrences.subscriber_id')
            ->where('subscribers.status', Subscriber::STATUS_ACTIVE)
            ->where('recurrences.payment_method', Recurrence::PAYMENT_METHOD_CREDIT_CARD)
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
            ->groupBy('subscriptions.id')
            ->whereNull('subscriptions.order_number') // legacy order without order_number
            ->get();

        Log::info(LogKeys::CHARGE_REGULAR_LEGACY_FOUND, ['value' => $recurrences->count()]);

        $total = 0;
        foreach ($recurrences as $recurrence) {
            $result = $this->subscriptionChargeService->dispatchSingleRecurrence($recurrence);
            if ($result) {
                $total++;

                Redis::incr(LogKeys::CHARGE_REGULAR_LEGACY_AFFECTED);
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
        Log::withContext(['legacy_mode' => true]);
        Log::withContext(['hostname-dispatcher' => gethostname()]);

        Log::info(LogKeys::CHARGE_REGULAR_LEGACY_LASTSTART, [
            'value' => Carbon::now()->toDateTimeString(),
        ]);

        Redis::set(LogKeys::CHARGE_REGULAR_LEGACY_LASTSTART, Carbon::now()->toDateTimeString());
        Redis::set(LogKeys::CHARGE_REGULAR_LEGACY_LASTTRACE, $uuid);

        // @todo Delete on ChargeLog deprecation
        ChargeLog::withContext(['schedule-trace-id' => $uuid]);
    }

    private function logEnd(int $total): void
    {
        Log::info(LogKeys::CHARGE_REGULAR_LEGACY_TOTAL, ['value' => $total]);
        Log::info(LogKeys::CHARGE_REGULAR_LEGACY_LASTEND, ['value' => Carbon::now()->toDateTimeString()]);

        Redis::set(LogKeys::CHARGE_REGULAR_LEGACY_TOTAL, $total);
        Redis::set(LogKeys::CHARGE_REGULAR_LEGACY_LASTEND, Carbon::now()->toDateTimeString());

        Log::withoutContext();
    }

}
