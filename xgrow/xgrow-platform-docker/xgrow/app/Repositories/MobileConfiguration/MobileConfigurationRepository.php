<?php

namespace App\Repositories\MobileConfiguration;

use App\MobileConfiguration;
use Illuminate\Support\Facades\Auth;

class MobileConfigurationRepository
{

    public function save(array $data)
    {
        return MobileConfiguration::updateOrCreate(
            [
                'platforms_users_id' => Auth::user()->id
            ],
            [
                'notifications' => $data['notifications'] === '1' ?? 0,
                'notifications_sells' => $data['notificationsSells'] === '1' ?? 0,
                'notifications_sells_product_name' => $data['notificationsSellsProductName'] === '1' ?? 0,
            ]
        );
    }

    public function show()
    {

        $notification = MobileConfiguration::where('platforms_users_id', Auth::user()->id)->first();

        if($notification)
            return [
                'notifications' => $notification->notifications === true ? 1:0,
                'notificationsSells' => $notification->notifications_sells === true ? 1:0,
                'notificationsSellsProductName' => $notification->notifications_sells_product_name === true ? 1:0,
            ];

        return [
            'notifications' => 1,
            'notificationsSells' => 1,
            'notificationsSellsProductName' => 1
        ];
    }
}
