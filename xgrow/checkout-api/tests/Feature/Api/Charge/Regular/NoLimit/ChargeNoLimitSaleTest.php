<?php

namespace Tests\Feature\Api\Charge\Regular\NoLimit;

use App\Payment;
use App\Services\Actions\RunRegularChargeForNoLimitAction;
use App\Services\Charges\NoLimitChargeService;
use App\Subscriber;
use App\Subscription;
use Carbon\Carbon;
use Tests\Feature\Helper\MundipaggToken;
use Tests\Feature\Traits\CreateSubscriberTrait;
use Tests\Feature\Traits\Integration\MockPubSubTrait;
use Tests\Feature\Traits\LocalDatabaseIds;
use Tests\TestCase;

class ChargeNoLimitSaleTest extends TestCase
{

    use MockPubSubTrait;
    use CreateSubscriberTrait;
    use LocalDatabaseIds;

    public function test_all_no_limit_retries_fail_and_no_repeat_in_same_day()
    {
        $this->withoutMiddleware();

        $this->mockPubSubWithCount(1);

        $subscriberId = $this->createSubscriber($this->platformId, $this->salePlanId);

        $token = MundipaggToken::insufficientBalance($this->faker->creditCardNumber('MasterCard'));

        $response = $this->postJson("/api/checkout/{$this->platformId}/{$this->salePlanId}", [
            'payment_method' => 'credit_card',
            'cc_info' => [
                [
                    'token' => "$token",
                    'installment' => 3,
                    'value' => '100.00'
                ]
            ],
            'subscriber_id' => $subscriberId,
            'platform_id' => $this->platformId,
            'plan_id' => $this->salePlanId,
            'order_bump' => []
        ]);

        if ($response->getStatusCode() == 409) {
            $this->markTestSkipped('Gateway problem');
        }

        $response->assertStatus(200);

        $this->assertDatabaseHas('subscribers', [
            'id' => $subscriberId,
            'status' => Subscriber::STATUS_ACTIVE,
        ]);

        $this->assertDatabaseHas('subscriptions', [
            'subscriber_id' => $subscriberId,
            'plan_id' => $this->salePlanId,
            'status' => Subscription::STATUS_ACTIVE,
        ]);

        $dueDate = Carbon::now()->addDays(30); // next recurrence: D

        //dump('renew on due date: forced fail');

        Carbon::setTestNow($dueDate);

        NoLimitChargeService::$forceFailStatusDebug = true;

        $action = new RunRegularChargeForNoLimitAction(['subscriber_id' => $subscriberId]);
        $action();

        $this->assertDatabaseHas('subscriptions', [
            'subscriber_id' => $subscriberId,
            'plan_id' => $this->salePlanId,
            'status' => Subscription::STATUS_FAILED,
        ]);

        $this->assertDatabaseHas('payments', [
            'subscriber_id' => $subscriberId,
            'attempts' => 1,
            'status' => Payment::STATUS_FAILED,
        ]);

        // try charging again in the same day

        $action = new RunRegularChargeForNoLimitAction(['subscriber_id' => $subscriberId]);
        $action();

        // assert not charged
        $this->assertDatabaseHas('payments', [
            'subscriber_id' => $subscriberId,
            'attempts' => 1,
            'status' => Payment::STATUS_FAILED,
        ]);
    }
}
