<?php

namespace Tests\Feature\Api\Charge\Ruler\CreditCard;

use App\Http\Controllers\Mundipagg\RecurrenceController;
use App\Services\Actions\RunChargeRulerForNoLimitAction;
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

class ChargeRulerNolimitSaleFailingTest extends TestCase
{

    use MockPubSubTrait;
    use CreateSubscriberTrait;
    use LocalDatabaseIds;

    protected function tearDown(): void
    {
        parent::tearDown();

        Carbon::setTestNow(); // IMPORTANT: Reset the date to not affect next tests
    }

    public function test_all_no_limit_retries_fail()
    {
        $this->withoutMiddleware();

        $this->mockPubSubWithCount(1);

        $subscriberId = $this->createSubscriber($this->platformId, $this->salePlanId);

        $token = MundipaggToken::insufficientBalance($this->faker->creditCardNumber('MasterCard'));

        $response = $this->postJson("/api/checkout/{$this->platformId}/{$this->salePlanId}", [
            "payment_method" => "credit_card",
            "cc_info" => [
                [
                    "token" => "$token",
                    "installment" => 3,
                    "value" => "100.00"
                ]
            ],
            "subscriber_id" => $subscriberId,
            "platform_id" => $this->platformId,
            "plan_id" => $this->salePlanId,
            "order_bump" => []
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
            //'status' => Subscription::STATUS_CANCELED, // subscription is not canceled anymore
            'status' => Subscription::STATUS_FAILED,
        ]);

        //dump('1st retry: forced fail');

        Carbon::setTestNow($dueDate->clone()->addDays(5)); // D+5

        $action5d = new RunChargeRulerForNoLimitAction(['subscriber_id' => $subscriberId]);
        $action5d();

        $this->assertDatabaseHas('subscriptions', [
            'subscriber_id' => $subscriberId,
            'plan_id' => $this->salePlanId,
            'status' => Subscription::STATUS_FAILED,
        ]);

        //dump('2nd retry: forced fail');

        Carbon::setTestNow($dueDate->clone()->addDays(12)); // D+12

        $action12d = new RunChargeRulerForNoLimitAction(['subscriber_id' => $subscriberId]);
        $action12d();

        $this->assertDatabaseHas('subscriptions', [
            'subscriber_id' => $subscriberId,
            'plan_id' => $this->salePlanId,
            'status' => Subscription::STATUS_FAILED,
        ]);

        //dump('3rd (and last) retry: forced fail, cancel subscription');

        Carbon::setTestNow($dueDate->clone()->addDays(20)); // D+20

        $action20d = new RunChargeRulerForNoLimitAction(['subscriber_id' => $subscriberId]);
        $action20d();

        $this->assertDatabaseHas('subscriptions', [
            'subscriber_id' => $subscriberId,
            'plan_id' => $this->salePlanId,
            //'status' => Subscription::STATUS_CANCELED,
            'status' => Subscription::STATUS_FAILED, // "Sem-Limite" subscription stays in "failed" status
        ]);
    }
}
