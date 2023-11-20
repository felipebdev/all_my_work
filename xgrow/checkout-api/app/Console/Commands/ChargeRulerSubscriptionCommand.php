<?php

namespace App\Console\Commands;

use App\Services\Actions\RunChargeRulerForSubscriptionsAction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChargeRulerSubscriptionCommand extends Command
{
    protected $signature = 'xgrow:charge-rules:subscription '.
    '{--platform_id= : Restrict to single platform} '.
    '{--subscriber_id= : Restrict to single subscriber} '.
    '{--payment_id= : Restrict to single payment} '.
    '{--dry-run : Run in test mode (no real transaction)} '.
    '{--base-date= : Use this as a base date (Y-m-d format)} '.
    '{--skip-email : Skip sending email} ';

    protected $description = 'Launches subscription charge rules';

    public function handle()
    {
        Log::withContext(['command_correlation_id' => (string) Str::uuid()]);

        Log::info('Subscription retry command starting');

        $debugOptions = [
            'platform_id' => $this->option('platform_id') ?? null,
            'subscriber_id' => $this->option('subscriber_id') ?? null,
            'payment_id' => $this->option('payment_id') ?? null,
            'dry-run' => $this->option('dry-run') ?? false,
            'base-date' => $this->option('base-date') ?? null,
            'skip-email' => $this->option('skip-email') ?? null,
        ];

        $action = new RunChargeRulerForSubscriptionsAction($debugOptions);
        $action();

        Log::info('Subscription retry command finished for all platforms');

        Log::withoutContext();
    }
}
