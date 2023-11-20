<?php

namespace Tests\Feature\Traits\Database;

use App\Payment;
use App\Subscriber;
use App\Subscription;

trait ValidatesPixDatabase
{
    private function assertDatabasePix(int $subscriberId, int $planId, $orderCode, $price): void
    {
        $this->assertDatabaseHas('subscribers', [
            'id' => $subscriberId,
            'status' => Subscriber::STATUS_LEAD,
        ]);

        $this->assertDatabaseHas('subscriptions', [
            'subscriber_id' => $subscriberId,
            'plan_id' => $planId,
            'status' => Subscription::STATUS_PENDING_PAYMENT,
        ]);

        $this->assertDatabaseHas('payments', [
            'order_code' => $orderCode,
            'status' => Payment::STATUS_PENDING,
            'price' => $price,
        ]);
    }
}
