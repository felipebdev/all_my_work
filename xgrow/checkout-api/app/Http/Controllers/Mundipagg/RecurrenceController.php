<?php

namespace App\Http\Controllers\Mundipagg;

use App\Constants\LogKeys;
use App\Http\Controllers\Controller;
use App\Logs\ChargeLog;
use App\Services\Actions\RunRegularChargeForLegacySubscriptionAction;
use App\Services\Actions\RunRegularChargeForNoLimitAction;
use App\Services\Actions\RunRegularChargeForSubscriptionAction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

/**
 * @deprecated Use specific actions instead
 */
class RecurrenceController extends Controller
{

    public function process()
    {
        Redis::set(LogKeys::CHARGE_REGULAR_RECURRENCES_LASTSTART, Carbon::now()->toDateTimeString());

        $this->processRecurrences();
        $this->processRecurrencesLegacy();
        $this->processUnlimitedSell();

        Redis::set(LogKeys::CHARGE_REGULAR_RECURRENCES_LASTEND, Carbon::now()->toDateTimeString());
    }

    public function processRecurrences()
    {
        ChargeLog::withContext(['schedule-trace-id' => (string) Str::uuid()]);
        ChargeLog::withContext(['running_origin' => 'charge_subscription']);

        $action = new RunRegularChargeForSubscriptionAction();
        $action();

        ChargeLog::withoutContext();
    }

    public function processRecurrencesLegacy()
    {
        ChargeLog::withContext(['schedule-trace-id' => (string) Str::uuid()]);
        ChargeLog::withContext(['running_origin' => 'charge_subscription']);
        ChargeLog::withContext(['legacy' => true]);

        $action = new RunRegularChargeForLegacySubscriptionAction();
        $action();

        ChargeLog::withoutContext();
    }

    /**
     * Process Unlimited order pending payments
     */
    public function processUnlimitedSell()
    {
        ChargeLog::withContext(['schedule-trace-id' => (string) Str::uuid()]);
        ChargeLog::withContext(['running_origin' => 'charge_nolimit']);

        $action = new RunRegularChargeForNoLimitAction();
        $action();

        ChargeLog::withoutContext();
    }
}
