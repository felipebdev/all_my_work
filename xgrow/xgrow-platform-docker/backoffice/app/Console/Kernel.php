<?php

namespace App\Console;

use App\Console\Commands\CreateForm;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Console\Command;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\cleanDirectoryPlatforms::class,
        Commands\createProductsByPlansData::class,
        Commands\populateDeliveriesTable::class,
        Commands\StandardizeCountryField::class,
        Commands\StandardizeCountryField::class,
        Commands\linkToPlatformSiteConfigs::class,
	    CreateForm::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('clean:platforms')
            ->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
