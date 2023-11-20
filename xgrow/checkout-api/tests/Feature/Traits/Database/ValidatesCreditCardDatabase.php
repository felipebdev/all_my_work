<?php

namespace Tests\Feature\Traits\Database;

use App\Payment;
use App\Subscriber;
use App\Subscription;

trait ValidatesCreditCardDatabase
{
    private function assertDatabaseCreditCard(int $subscriberId, int $planId, $orderCode, $price): void
    {
        $this->assertDatabaseHas('subscribers', [
            'id' => $subscriberId,
            'status' => Subscriber::STATUS_ACTIVE,
        ]);

        $this->assertDatabaseHas('subscriptions', [
            'subscriber_id' => $subscriberId,
            'plan_id' => $planId,
            'status' => Subscription::STATUS_ACTIVE,
        ]);

        $this->assertDatabaseHas('payments', [
            'order_code' => $orderCode,
            'status' => Payment::STATUS_PAID,
            'price' => $price,
        ]);
    }
}
