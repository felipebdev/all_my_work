<?php

namespace App\Console;

use App\Console\Commands\NotifyUpcomingRecurrenceCommand;
use App\Services\Actions\PagarmeChargebackUpdate;
use App\Services\Actions\AbandonedCartAction;
use App\Services\Actions\ExpirePaymentsAndCancelSubscriptions;
use App\Services\Actions\NotifyExpiredBoletoPixAction;
use App\Services\Actions\NotifyUpcomingBoletoPixAction;
use App\Services\Actions\NotifyUpComingRecurrence;
use App\Services\Actions\RunChargeRulerForNoLimitAction;
use App\Services\Actions\RunChargeRulerForSubscriptionsAction;
use App\Services\Actions\RunFiveDaysExpiredPaymentsCancelSubscriptions;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\chargebackWithoutDate::class,
        Commands\PaymentApprovedEventCommand::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
