<?php

namespace App\Jobs;

use App\Recurrence;
use App\Services\Charges\SubscriptionBoletoPixNotificationExpiredService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BoletoPixExpiredNotificationJob implements ShouldQueue
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

    public function handle(SubscriptionBoletoPixNotificationExpiredService $service)
    {
        $service->handleEmailNotification($this->recurrence);
    }
}
