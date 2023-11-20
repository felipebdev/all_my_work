<?php

namespace App\Console\Commands;

use App\Services\Actions\RunRegularChargeForNoLimitAction;
use Carbon\CarbonPeriod;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RegularChargeNoLimitCommand extends Command
{
    protected $signature = 'xgrow:regular-charge:nolimit '.
    '{--platform_id= : Restrict to single platform} '.
    '{--subscriber_id= : Restrict to single subscriber} '.
    '{--payment_ids= : Restrict to payments, separated by comma (,)} '.
    '{--dry-run : Run in test mode (no real transaction)} '.
    '{--base-date= : Use this as a base date (Y-m-d format), conflicts with --interval-dates} '.
    '{--interval-dates= : Use this as a base date interval (Y-m-d,Y-m-d format), conflicts with --base-date} '.
    '{--skip-email : Skip sending email} '.
    '{--update-date : Update payments.payment_date on success} ';

    protected $description = 'Launches regular no limit charge';

    public function handle()
    {
        Log::withContext(['command_correlation_id' => (string) Str::uuid()]);

        Log::info('No-Limit regular command starting');

        $baseDate = $this->option('base-date') ?? [];
        $intervalDates = $this->option('interval-dates') ?? null;

        if ($baseDate && $intervalDates) {
            $this->error("--base-date and --interval-dates can't be used at the same time");
            return Command::FAILURE;
        }

        if ($intervalDates) {
            $dates = $this->generateDatesFromInterval($intervalDates);
        } elseif ($baseDate) {
            $dates = [$baseDate];
        } else {
            $dates = [null];
        }

        foreach ($dates as $date) {
            Log::info("No-Limit regular command started for date {$date}");

            $debugOptions = [
                'platform_id' => $this->option('platform_id') ?? null,
                'subscriber_id' => $this->option('subscriber_id') ?? null,
                'payment_ids' => explode(',', $this->option('payment_ids') ?? ''),
                'dry-run' => $this->option('dry-run') ?? false,
                'base-date' => $date,
                'skip-email' => $this->option('skip-email') ?? null,
                'update-date' => $this->option('update-date') ?? null,
            ];

            $action = new RunRegularChargeForNoLimitAction($debugOptions);
            $action();
        }

        Log::info('No-Limit regular command finished for all platforms');

        Log::withoutContext();
    }

    private function generateDatesFromInterval(string $dateInterval): ?array
    {
        $interval = explode(',', $dateInterval);
        if (count($interval) != 2) {
            $message = 'Start and end must be separated by comma (,)';
            $this->error($message);
            throw new Exception($message);
        }

        $period = CarbonPeriod::create($interval[0], $interval[1]);

        return collect($period)->map->format('Y-m-d')->toArray();
    }
}
