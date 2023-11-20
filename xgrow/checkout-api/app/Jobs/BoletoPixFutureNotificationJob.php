<?php

namespace App\Jobs;

use App\Recurrence;
use App\Services\Charges\SubscriptionBoletoPixNotificationFutureService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

//use Illuminate\Contracts\Queue\ShouldBeUnique;

class BoletoPixFutureNotificationJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public Recurrence $recurrence;

    public function __construct(Recurrence $recurrence)
    {
        $this->recurrence = $recurrence;
    }

    public function handle(SubscriptionBoletoPixNotificationFutureService $service)
    {
        $service->handleEmailNotification($this->recurrence);
    }
}
