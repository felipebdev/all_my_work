<?php

namespace App\Repositories\MobileNotifications;

use App\MobileNotification;
use Illuminate\Support\Facades\Auth;

/**
 *
 */
class MobileNotificationRepository
{

    /**
     * @return mixed
     */
    public function getCountNotifications()
    {
        return $this->queryNotifications()->where('read', false)->count();
    }

    /**
     * @param bool $paginate
     * @return mixed
     */
    public function getNotificationsByUserPlatform(bool $paginate = false)
    {
        return $paginate === false ? $this->queryNotifications()->get() : $this->queryNotifications()->paginate(20);
    }

    /**
     * @param $mobileNotificationId
     * @param int $read
     * @return mixed
     */
    public function readMobileNotification($mobileNotificationId, int $read = 1)
    {
        $mobileNotification = MobileNotification::find($mobileNotificationId);

        $mobileNotification->read = $read;
        $mobileNotification->save();

        return $mobileNotification;
    }

    /**
     * @param $mobileNotificationId
     * @param $read
     * @return mixed
     */
    public function updateStatus($mobileNotificationId, $read)
    {
        return $this->readMobileNotification($mobileNotificationId, $read);
    }

    /**
     * @return mixed
     */
    public function queryNotifications()
    {
        return MobileNotification::where('platform_id', Auth::user()->platform_id)
            ->where('platforms_users_id', Auth::user()->id)
            ->orderBy('read', 'ASC')
            ->orderBy('created_at', 'ASC');
    }
}
