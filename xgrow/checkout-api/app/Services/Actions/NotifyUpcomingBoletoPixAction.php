<?php

namespace App\Services\Actions;

use App\Logs\ChargeLog;
use App\Services\Charges\SubscriptionBoletoPixNotificationFutureService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NotifyUpcomingBoletoPixAction extends NotifyBoletoPixActionTemplate
{

    public const NOTIFICATION_DAYS = 3;

    private SubscriptionBoletoPixNotificationFutureService $notificationService;

    public function __construct()
    {
        $this->notificationService = app()->make(SubscriptionBoletoPixNotificationFutureService::class);
    }

    public function __invoke()
    {
        $uuid = (string) Str::uuid();
        ChargeLog::withContext(['schedule-trace-id' => $uuid]);
        ChargeLog::withContext(['running_origin' => 'notify_subscription']);
        ChargeLog::withContext(['hostname-dispatcher' => gethostname()]);

        Log::withContext(['correlation_id' => $uuid]);

        ChargeLog::info('Processing boleto/pix notification started');

        // renew date in next N days (including renew date)
        $begin = Carbon::now()->toDateString();
        $end = Carbon::now()->addDays(self::NOTIFICATION_DAYS)->toDateString();

        $recurrences = $this->getRecurrencesWithNextPaymentDateBetween($begin, $end);

        foreach ($recurrences as $recurrence) {
            $this->notificationService->dispatchSingleRecurrenceNotification($recurrence);
        }

        return true;
    }
}
