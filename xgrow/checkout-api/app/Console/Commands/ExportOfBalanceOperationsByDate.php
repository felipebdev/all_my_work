<?php

namespace App\Console\Commands;

use App\Jobs\ExportOfBalanceOperationsByDateIntervalJob;
use App\Jobs\ExportOfBalanceOperationsJob;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PagarMe\Client;
use Carbon\Carbon;

class ExportOfBalanceOperationsByDate extends Command
{
    /**
     * Usage example:
     *
     * php artisan xgrow:export-of-balance-operation-by-date --initial_date=2022-01-01 --final_date=2022-12-31
     *
     */
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'xgrow:export-of-balance-operation-by-date '.
    '{--initial_date= : Date initial} '.
    '{--final_date= : Date final} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for export of balance operation spreadsheet by date interval';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $initialDate = $this->option('initial_date') ?? null;
        $finalDate = $this->option('final_date') ?? null;

        if ( is_null($initialDate) || is_null($finalDate)) {
            echo 'Date interval is incorrect.';
            return;
        }

        $startDate = Carbon::parse($initialDate);
        $endDate = Carbon::parse($finalDate);
        $numMonths = $startDate->diffInMonths($endDate);

        $datesInterval = [];

        if ($numMonths > 1) {
            for ($i = 0; $i <= $numMonths; $i++) {
                echo $startDate->format('M Y') . ' - ';

                $datesInterval[] = [
                    'firstDay' => $startDate->clone()->startOfMonth()->format('Y-m-d 00:00:00'),
                    'lastDay' => $startDate->clone()->endOfMonth()->format('Y-m-d 11:59:59')
                ];

                $startDate->addMonth();
            }
        } else {
            $datesInterval[] = ['firstDay' => $startDate->format('Y-m-d'), 'lastDay' => $endDate->format('Y-m-d')];
        }

        $correlationId = (string) Str::uuid();
        $context = ['command_correlation_id' => $correlationId, 'command'=>'balance-operation'];

        Log::withContext($context);

        Log::info('Command for export of balance operation spreadsheet');

        foreach($datesInterval as $date) {
            ExportOfBalanceOperationsByDateIntervalJob::dispatch($date['firstDay'], $date['lastDay'], $context);
        }

        Log::info('Command for export of balance operation spreadsheet finished');

        Log::withoutContext();
    }
}
