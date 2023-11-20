<?php

namespace App\Listeners\Audit;

use App\AccessLog;
use App\Facades\AccessLogFacade;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogSuccessfulLogout
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {   
        AccessLogFacade::build($event->user)
            ->logSuccessfulLogout();
    }
}
