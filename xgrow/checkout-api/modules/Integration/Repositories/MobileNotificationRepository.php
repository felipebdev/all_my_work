<?php

namespace Modules\Integration\Repositories;

use App\MobileNotification;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Modules\Integration\Services\Objects\ExpoMessage;

class MobileNotificationRepository
{

    public function saveNotification(
        string $platformId,
        string $platformUserId,
        ExpoMessage $message
    ): MobileNotification {
        return MobileNotification::create([
            'platform_id' => $platformId,
            'platforms_users_id' => $platformUserId,
            'title' => $message->title,
            'body' => $message->body,
        ]);
    }

    public function listUserNotifications(string $platformUserId, ?string $platformId = null): Collection
    {
        return MobileNotification::where('platform_user_id', $platformUserId)
            ->when($platformId, function ($q, $platformId) {
                $q->where('platform_id', $platformId);
            })
            ->get();
    }

    public function markAsRead(string $notificationId): int
    {
        return MobileNotification::where('id', $notificationId)->update([
            'updated_at' => Carbon::now(),
            'read' => true,
        ]);
    }

    public function markAllAsRead(string $platformUserId, ?string $platformId = null): int
    {
        return MobileNotification::where('platform_user_id', $platformUserId)
            ->when($platformId, function ($q, $platformId) {
                $q->where('platform_id', $platformId);
            })->update([
                'updated_at' => Carbon::now(),
                'read' => true,
            ]);
    }


}
