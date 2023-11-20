<?php

namespace App\Console\Commands;

use App\PushNotification;
use App\Subscriber;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Integration\Services\ExpoPublisher;
use Modules\Integration\Services\Objects\ExpoMessage;

class ExpoMobilePushNotificationCommand extends Command
{
    protected $signature = 'xgrow:expo:push-notifications '.
    '{--platform_id= : Restrict to single platform} '.
    '{--subscriber_id= : Restrict to single subscriber} ';

    protected $description = 'Command description';

    private ExpoPublisher $expoPublisher;

    public function handle(ExpoPublisher $expoPublisher)
    {
        $correlationId = (string) Str::uuid();

        Log::withContext(['command_correlation_id' => $correlationId]);

        Log::info('Expo Push Notification command starting');

        $this->expoPublisher = $expoPublisher;
        $this->expoPublisher->withCorrelationId($correlationId);

        $notifications = $this->getReadyNotifications();

        $affected = [];
        foreach ($notifications as $notification) {
            $affected[] = $this->handleSingleNotification($notification);
        }

        $totalAffected = array_sum($affected);

        Log::info('Expo Push Notification command finished for all platforms', [
            'total_affected' => $totalAffected,
        ]);

        Log::withoutContext();

        return self::SUCCESS;
    }

    /**
     * @param  \App\PushNotification  $notification
     * @return int  Number of subscribers affected
     */
    protected function handleSingleNotification(PushNotification $notification): int
    {
        $platformId = $notification->platform_id;

        Log::info('Expo push notification for platform initiated', [
            'platform_id' => $platformId,
            'notification_id' => $notification->id ?? null,
        ]);

        $subscriberTokens = $this->getSubscriberExpoLaTokens($platformId);

        $totalTokens = $subscriberTokens->count();

        if ($totalTokens == 0) {
            Log::info('Expo push notification for platform skipped (no tokens)', [
                'platform_id' => $platformId,
                'notification_id' => $notification->id ?? null,
            ]);

            return 0;
        }

        $expoTokens = $subscriberTokens->pluck('expo_la_token');

        $message = new ExpoMessage($notification->title, $notification->text);

        $this->expoPublisher->pushNotification($platformId, $message, $expoTokens);

        $notification->is_sent = true;
        $notification->save();

        Log::info('Expo push notification for platform finished', [
            'platform_id' => $platformId,
            'notification_id' => $notification->id ?? null,
            'total_tokens' => $totalTokens,
        ]);

        return $totalTokens;
    }

    /**
     * Get ready notifications
     *
     * @return \Illuminate\Support\Collection
     */
    private function getReadyNotifications(): Collection
    {
        return PushNotification::query()
            ->where('is_sent', false)
            ->where('run_at', '<=', Carbon::now())
            ->when($this->option('platform_id'), function ($query, $platformId) {
                $query->where('platform_id', $platformId);
            })
            ->get();
    }

    /**
     * Get tokens from subscribers in a given platform
     *
     * @param  string  $platformId
     * @return \Illuminate\Support\Collection
     */
    private function getSubscriberExpoLaTokens(string $platformId): Collection
    {
        return Subscriber::query()
            ->where('platform_id', $platformId)
            ->whereNotNull('expo_la_token')
            ->when($this->option('subscriber_id'), function ($query, $subscriberId) {
                $query->where('subscriber_id', $subscriberId);
            })
            ->get('expo_la_token');
    }


}
