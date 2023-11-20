<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RunFiveDaysExpiredPaymentsCancelSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'xgrow:run-five-days-expired-payments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run five days expired payments cancel subscriptions';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::withContext(['command_correlation_id' => (string) Str::uuid()]);

        Log::info('run five days expired payments cancel subscriptions command starting');

        $action = new \App\Services\Actions\RunFiveDaysExpiredPaymentsCancelSubscriptions();
        $action();

        Log::info('run five days expired payments cancel subscriptions finished for all platforms');

        Log::withoutContext();
    }
}
