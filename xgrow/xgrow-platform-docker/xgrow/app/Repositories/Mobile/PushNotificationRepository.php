<?php

namespace App\Repositories\Mobile;

use App\PushNotification;
use App\Repositories\Mobile\Filter\PushNotificationFilter;
use Illuminate\Database\Eloquent\Collection;

class PushNotificationRepository
{
    /**
     * @param  string  $platformId
     * @param  string  $userId
     * @param  string  $title
     * @param  string  $text
     * @param  \DateTimeInterface  $runAt
     * @return \App\PushNotification
     */
    public function createMobileNotification(
        string $platformId,
        string $userId,
        string $title,
        string $text,
        \DateTimeInterface $runAt
    ): PushNotification {
        return PushNotification::create([
            'title' => $title,
            'text' => $text,
            'run_at' => $runAt,
            'platform_id' => $platformId,
            'is_sent' => false,
            'user_id' => $userId,
            'type' => PushNotification::TYPE_MOBILE,
        ]);
    }

    /**
     * @param  string  $notificationId
     * @return bool|null  true if sent, false if not, null if undefined (not found)
     */
    public function isNotificationSent(string $notificationId): ?bool
    {
        $notification = PushNotification::query()->findOrFail($notificationId);

        if (is_null($notification)) {
            return null;
        }

        return (bool) $notification->is_sent;
    }

    /**
     * @param  string  $notificationId
     * @param  array  $update
     * @return int
     */
    public function updateMobileNotificationById(string $notificationId, array $update): PushNotification
    {
        $notification = PushNotification::where('id', $notificationId)->first();
        $notification->update($update);
        return $notification;
    }

    /**
     * @param  string  $notificationId
     * @return bool true if was successfully deleted, false otherwise
     */
    public function softDeleteById(string $notificationId): bool
    {
        $notification = PushNotification::where('id', $notificationId)->first();

        if (is_null($notification)) {
            return false; // not found means not success
        }

        return (bool) $notification->delete();
    }

    /**
     * List all push notifications using given criteria
     *
     * @param  \App\Repositories\Mobile\Filter\PushNotificationFilter  $filter
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function listAllMobileNotifications(PushNotificationFilter $filter): Collection
    {
        $direction = $filter->is_sent ? 'DESC' : 'ASC'; // DESC if is_sent=1 (historical, most recent on top)

        return PushNotification::query()
            ->whereNull('deleted_at') // skip soft-deleted
            ->where('type', '=', PushNotification::TYPE_MOBILE)
            ->when($filter->platform_id, function ($query, $platformId) {
                $query->where('platform_id', '=', $platformId);
            })
            ->when($filter->title, function ($query, $title) {
                $query->where('title', '=', $title);
            })
            ->when($filter->text, function ($query, $text) {
                $query->where('text', '=', $text);
            })
            ->when($filter->run_after, function ($query, $start) {
                $query->where('run_after', '>', $start);
            })
            ->when($filter->run_before, function ($query, $end) {
                $query->where('run_before', '<', $end);
            })
            ->when(!is_null($filter->is_sent), function ($query, $isSentSet) use ($filter) {
                $query->where('is_sent', '=', (bool) $filter->is_sent);
            })
            ->when($filter->user_id, function ($query, $userIds) {
                $query->whereIn('user_id', $userIds);
            })
            ->when($filter->created_after, function ($query, $start) {
                $query->where('created_after', '>', $start);
            })
            ->when($filter->created_before, function ($query, $end) {
                $query->where('created_before', '<', $end);
            })
            ->when($filter->updated_after, function ($query, $start) {
                $query->where('updated_after', '>', $start);
            })
            ->when($filter->updated_before, function ($query, $end) {
                $query->where('updated_before', '<', $end);
            })
            ->when($filter->search, function ($query, $searchTerm) {
                $query->where(function ($query) use ($searchTerm) {
                    $query->orWhere('push_notifications.title', 'like', "%{$searchTerm}%")
                        ->orWhere('push_notifications.text', 'like', "%{$searchTerm}%");
                });
            })
            ->orderBy('push_notifications.run_at', $direction)
            ->get();
    }

}
