<?php

namespace App\Console;

use App\Console\Commands\NoLimitEmailChargeCommand;
use App\Console\Commands\NotifyUpcomingRecurrenceCommand;
use App\Console\Commands\SubscriptionEmailChargeCommand;
use App\Http\Controllers\Mundipagg\RecurrenceController;
use App\Services\Campaign\CampaignScheduleExecutor;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Modules\Integration\Commands\OAuthRefreshTokenCommand;
use Modules\Integration\Enums\CodeEnum;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\canceledSubscriptionsPendents::class,
        Commands\subscriptionsPendents::class,
        Commands\importChargesGetnet::class,
        Commands\importSubscriptionsGetnet::class,
        Commands\logoutSubscribersInactives::class,
        Commands\PaymentApprovedEventCommand::class,
        Commands\CheckSubscriberNeverAccessed::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //FIXME Removed column login because is not updated
        //$schedule->command('subscribers:never-accessed')->dailyAt('03:00');

        //Run schedule commands only on S1
        //FIXME Remove mongo acess log. Update data in api login. \App\Http\Controllers\AuthController::login
        /*if (gethostname() == "s1fandone") {
            $schedule->command(CompatibilityImportAccessLogFromMongodb::class)
                ->everyThirtyMinutes();
        }*/

        if ($this->shouldRunCampaign()) {
            $schedule->call(function () {
                $campaignScheduleExecutor = new CampaignScheduleExecutor();
                $campaignScheduleExecutor->startPendingCampaigns();
            })->everyMinute();
        }

        // Update the chargeback status from pagarme
        //        $schedule->call(function () {
        //            PagarmeChargebackUpdate::dispatch();
        //        })->everyMinute()->name('update-chargeback')->onOneServer();

        // Moved to checkout-api
        //$schedule->job(new PagarmeChargebackUpdate)->dailyAt('02:00');

        // Send Email to subscriber not access the course in 3 and 7 days
        /**
         * @deprecated
         * @override SubscriberNeverAccessEmailChargeCommand
         */
        //$schedule->call(function () {
        //    (new SubscriberNotificationController)->sendNotAccessedCourseEmail();
        //})->dailyAt('04:00');

        // Moved to checkout-api
        // Charge of recurrences on due date
        //$schedule->call(function () {
        //    $recurrenceController = new RecurrenceController();
        //    $recurrenceController->process();
        //})->dailyAt('10:30')->name('recurrence-charge')->onOneServer();

        //Charge Ruler Crons
        //$schedule->command(BankSlipEmailChargeCommand::class)
        //    ->dailyAt('10:00')
        //    ->onOneServer()
        //    ->runInBackground();

        // Moved to checkout-api
        //$schedule->command(SubscriptionEmailChargeCommand::class)
        //    ->dailyAt('9:30')
        //    ->onOneServer()
        //    ->runInBackground();

        // Moved to checkout-api
        //$schedule->command(NoLimitEmailChargeCommand::class)
        //    ->dailyAt('9:45')
        //    ->onOneServer()
        //    ->runInBackground();

        //$schedule->command(SubscriberNeverAccessEmailChargeCommand::class)
        //    ->dailyAt('10:15')
        //    ->onOneServer()
        //    ->runInBackground();

        // Moved to checkout-api
        //$schedule->command(NotifyUpcomingRecurrenceCommand::class)
        //    ->dailyAt('10:30')
        //    ->onOneServer()
        //    ->runInBackground();

        /**
         * Update oauth integration's tokens
         */
        $providers = implode(',', [CodeEnum::INFUSION]);
        $schedule->command(OAuthRefreshTokenCommand::class, [$providers])
            ->dailyAt('01:00')
            ->onOneServer()
            ->runInBackground();
    }

    private function shouldRunCampaign(): bool
    {
        if ($this->app->environment() != 'production') {
            // FakeServices will handle it
            return true;
        }

        if ($this->app->environment() == 'production' && gethostname() == "s1fandone") {
            // Run only on single instance
            return true;
        }

        return false;
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
