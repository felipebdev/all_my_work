<?php

namespace App\Jobs;

use App\Recurrence;
use App\Services\Mundipagg\RecurrenceOrderService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MundipaggRecurrenceOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    public $recurrence;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Recurrence $recurrence)
    {
        $this->recurrence = $recurrence;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $recurrenceOrderService = new RecurrenceOrderService();
        $recurrenceOrderService->createRecurrenceOrder($this->recurrence);
    }
}
