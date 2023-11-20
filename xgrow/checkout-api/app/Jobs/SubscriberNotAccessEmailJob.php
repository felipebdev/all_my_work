<?php

namespace App\Jobs;

use App\Platform;
use App\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class SubscriberNotAccessEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $subscribers;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->subscribers = DB::table('subscribers')->whereNull('last_acess');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

    }
}
