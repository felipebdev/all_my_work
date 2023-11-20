<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NotifyUpComingBoletoPixAction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'xgrow:notify-up-coming-boleto-pix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify up coming boleto pix action';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::withContext(['command_correlation_id' => (string) Str::uuid()]);

        Log::info('Notify up coming boleto pix action command starting');

        $action = new \App\Services\Actions\NotifyUpcomingBoletoPixAction();
        $action();

        Log::info('Notify up coming boleto pix action command finished for all platforms');

        Log::withoutContext();
    }
}
