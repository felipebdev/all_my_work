<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ExpirePaymentsAndCancelSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'xgrow:expire-payments-and-cancel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire payments and cancel subscriptions';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::withContext(['command_correlation_id' => (string) Str::uuid()]);

        Log::info('Expire payments and cancel subscriptions command starting');

        $action = new \App\Services\Actions\ExpirePaymentsAndCancelSubscriptions();
        $action();

        Log::info('Expire payments and cancel subscriptions command finished for all platforms');

        Log::withoutContext();
    }
}
