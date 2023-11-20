<?php

namespace App\Console\Commands;

use App\Services\Actions\NotifyUpComingRecurrence;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class NotifyUpcomingRecurrenceCommand extends Command
{
    protected $signature = 'xgrow:upcoming:recurrence '.
    '{--subscriber_id= : Restrict to single subscriber} '.
    '{--id= : Restrict to single recurrence_id (subscription)/payment_id (no-limit)} '.
    '{--days=15 : Specify number of days} '.
    '{--dry-run : Run in test mode (no real mail sending)} ';

    protected $description = 'Notify user about upcoming charge of subscription/no-limit';

    public function handle()
    {

        Log::info('Notify user about upcoming charge command starting');

        $action = new NotifyUpComingRecurrence(
            $this->option('dry-run'),
            $this->option('days'),
            $this->option('subscriber_id'),
            $this->option('id')
        );
        $action();

        Log::info('Notify user about upcoming charge command finished for all platforms');

        Log::withoutContext();

    }


}
