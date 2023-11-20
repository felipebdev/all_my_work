<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AbandonedCartAction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'xgrow:abandoned-cart-action';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Abandoned cart action command';

    public function handle()
    {

        Log::info('Abandoned cart action command starting');

        $action = new \App\Services\Actions\AbandonedCartAction();
        $action();

        Log::info('Abandoned cart action command finished for all platforms');

        Log::withoutContext();

    }
}
