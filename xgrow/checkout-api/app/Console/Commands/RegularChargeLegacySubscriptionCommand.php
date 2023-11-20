<?php

namespace App\Console\Commands;

use App\Services\Actions\RunRegularChargeForLegacySubscriptionAction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RegularChargeLegacySubscriptionCommand extends Command
{
    protected $signature = 'xgrow:regular-charge:legacy-subscription '.
    '{--platform_id= : Restrict to single platform} '.
    '{--subscriber_id= : Restrict to single subscriber} '.
    '{--recurrence_id= : Restrict to single recurrence} '.
    '{--dry-run : Run in test mode (no real transaction)} '.
    '{--base-date= : Use this as a base date (Y-m-d format)} '.
    '{--skip-email : Skip sending email} ';

    protected $description = 'Launches regular (legacy) subscription charge';

    public function handle()
    {
        Log::withContext(['command_correlation_id' => (string) Str::uuid()]);

        Log::info('Subscription regular command starting');

        $debugOptions = [
            'platform_id' => $this->option('platform_id') ?? null,
            'subscriber_id' => $this->option('subscriber_id') ?? null,
            'recurrence_id' => $this->option('recurrence_id') ?? null,
            'dry-run' => $this->option('dry-run') ?? false,
            'base-date' => $this->option('base-date') ?? null,
            'skip-email' => $this->option('skip-email') ?? null,
        ];

        $action = new RunRegularChargeForLegacySubscriptionAction($debugOptions);
        $action();

        Log::info('Subscription regular command finished for all platforms');

        Log::withoutContext();
    }
}
