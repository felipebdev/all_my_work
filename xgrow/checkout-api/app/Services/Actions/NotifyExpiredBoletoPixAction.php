<?php

namespace App\Services\Actions;

use App\Logs\ChargeLog;
use App\Services\Charges\SubscriptionBoletoPixNotificationExpiredService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NotifyExpiredBoletoPixAction extends NotifyBoletoPixActionTemplate
{

    public const NOTIFICATION_DAYS = 5;

    private SubscriptionBoletoPixNotificationExpiredService $notificationService;

    public function __construct()
    {
        $this->notificationService = app()->make(SubscriptionBoletoPixNotificationExpiredService::class);
    }

    public function __invoke()
    {
        $uuid = (string) Str::uuid();
        ChargeLog::withContext(['schedule-trace-id' => $uuid]);
        ChargeLog::withContext(['running_origin' => 'notify_subscription']);
        ChargeLog::withContext(['hostname-dispatcher' => gethostname()]);

        Log::withContext(['correlation_id' => $uuid]);

        ChargeLog::info('Processing boleto/pix notification started');

        // renew date in past N days
        $begin = Carbon::now()->subDays(self::NOTIFICATION_DAYS)->toDateString();
        $end = Carbon::now()->subDays(1)->toDateString();

        $recurrences = $this->getRecurrencesWithNextPaymentDateBetween($begin, $end);

        foreach ($recurrences as $recurrence) {
            $this->notificationService->dispatchSingleRecurrenceNotification($recurrence);
        }

        return true;
    }

}
