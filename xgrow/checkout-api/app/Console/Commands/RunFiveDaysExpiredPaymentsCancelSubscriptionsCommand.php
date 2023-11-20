<?php

namespace App\Console\Commands;

use App\Services\Actions\RunFiveDaysExpiredPaymentsCancelSubscriptions;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RunFiveDaysExpiredPaymentsCancelSubscriptionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'xgrow:run-five-days-cancel-subscriptions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for find expired payments after 5 days and cancel subscription';

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
        Log::withContext(['command_correlation_id' => (string) Str::uuid()]);

        Log::info('Expire payments after 5 days and cancel subscription command starting');

        $action = new RunFiveDaysExpiredPaymentsCancelSubscriptions();
        $action();

        Log::info('Expire payments after 5 days and cancel subscription for all platforms');

        Log::withoutContext();

        return Command::SUCCESS;
    }
}
