<?php

namespace App\Console\Commands;

use App\Http\Controllers\Mundipagg\RecurrenceController;
use App\Logs\ChargeLog;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * @deprecated Use specific commands instead
 */
class RecurrenceRegularChargeCommand extends Command
{
    protected $signature = 'xgrow:regular-charge:recurrence';

    protected $description = 'Launches regular recurrence charge (subscription and no-limit)';

    public function handle()
    {
        $this->warn('This command is DEPRECATED and will be REMOVED in future');

        ChargeLog::withContext(['command_correlation_id' => (string) Str::uuid()]);

        ChargeLog::info('Recurrence regular command starting');

        $recurrenceController = new RecurrenceController();
        $recurrenceController->process();

        ChargeLog::info('Recurrence regular command finished for all platforms');

        ChargeLog::withoutContext();
    }
}
