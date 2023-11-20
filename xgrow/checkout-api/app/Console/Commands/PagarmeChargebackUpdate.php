<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PagarmeChargebackUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'xgrow:pagarme-chargeback-update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pagarme chargeback update';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::withContext(['command_correlation_id' => (string) Str::uuid()]);

        Log::info('Pagarme chargeback update command starting');

        $action = new \App\Services\Actions\PagarmeChargebackUpdate();
        $action();

        Log::info('Pagarme chargeback update command finished for all platforms');

        Log::withoutContext();
    }
}
