<?php

namespace App\Console\Commands;

use App\Logs\ChargeLog;
use App\Services\Actions\RunChargeRulerForSubscriptionsAction;
use App\Services\Actions\RunRegularChargeForSubscriptionAction;
use App\Services\Mundipagg\CreditCardRecurrenceService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Queue\QueueManager;
use Illuminate\Support\Str;

/**
 * Usage example:
 *
 * php artisan xgrow:fix:sx59 --log-only 2022-07-21
 *
 */
class FixSx59SubscriptionsCommand extends Command
{
    protected $signature = 'xgrow:fix:sx59 {date} '.
    '{--platform_id= : Restrict to single platform} '.
    '{--subscriber_id= : Restrict to single subscriber} '.
    '{--payment_id= : Restrict to single payment} '.
    '{--log-only : no real transaction, only logs} ';

    protected $description = 'Fixes SX-59 subscriptions';

    public function handle(QueueManager $queueManager)
    {
        $queueManager->setDefaultDriver('sync');

        ChargeLog::withContext(['command_correlation_id' => (string) Str::uuid()]);

        ChargeLog::info('SX-59 command starting');

        $debugOptions = [
            'platform_id' => $this->option('platform_id') ?? null,
            'subscriber_id' => $this->option('subscriber_id') ?? null,
            'payment_id' => $this->option('payment_id') ?? null,
            'base-date' => $this->argument('date'), // important!
            'skip-email' => true,
        ];

        CreditCardRecurrenceService::$useExistingOrder = true; // !!!!

        if ($this->option('log-only') ?? false) {
            CreditCardRecurrenceService::$logOnly = true;
        }

        $base = Carbon::createFromFormat('Y-m-d', $this->argument('date'))->setTime(10, 29, 0);
        Carbon::setTestNow($base);

        ChargeLog::withContext([
            'retroactive' => true,
            'date' => $this->argument('date')
        ]);

        $action = new RunRegularChargeForSubscriptionAction($debugOptions);
        $action();

        ChargeLog::info('SX-59 command finished for all platforms');

        ChargeLog::withoutContext();
    }
}
