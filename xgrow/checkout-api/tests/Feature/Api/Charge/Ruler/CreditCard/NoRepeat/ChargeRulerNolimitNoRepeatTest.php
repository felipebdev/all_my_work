<?php

namespace Tests\Feature\Api\Charge\Ruler\CreditCard\NoRepeat;

use App\Services\Actions\RunChargeRulerForNoLimitAction;
use App\Services\Actions\RunRegularChargeForNoLimitAction;
use App\Services\Charges\NoLimitChargeService;
use Carbon\Carbon;
use Tests\Feature\Helper\MundipaggToken;
use Tests\Feature\Traits\CreateSubscriberTrait;
use Tests\Feature\Traits\Integration\MockPubSubTrait;
use Tests\Feature\Traits\LocalDatabaseIds;
use Tests\TestCase;

class ChargeRulerNolimitNoRepeatTest extends TestCase
{

    use MockPubSubTrait;
    use CreateSubscriberTrait;
    use LocalDatabaseIds;

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

        $dueDate = Carbon::now()->addDays(30); // next recurrence: D

        // renew on due date: forced fail

        Carbon::setTestNow($dueDate);

        NoLimitChargeService::$forceFailStatusDebug = true;

        $action = new RunRegularChargeForNoLimitAction(['subscriber_id' => $subscriberId]);
        $action();

        // assert failed on due date
        $this->assertDatabaseHas('payments', [
            'subscriber_id' => $subscriberId,
            'status' => 'failed',
            'attempts' => 1,
        ]);

        // 1st retry: forced fail

        Carbon::setTestNow($dueDate->clone()->addDays(5)); // D+5

        $action5d = new RunChargeRulerForNoLimitAction(['subscriber_id' => $subscriberId]);
        $action5d();

        // assert that second error was registered
        $this->assertDatabaseHas('payments', [
            'subscriber_id' => $subscriberId,
            'status' => 'failed',
            'attempts' => 2,
        ]);

        $action5d = new RunChargeRulerForNoLimitAction(['subscriber_id' => $subscriberId]);
        $action5d();

        // assert that another retry wasn't affected
        $this->assertDatabaseHas('payments', [
            'subscriber_id' => $subscriberId,
            'status' => 'failed',
            'attempts' => 2,
        ]);

        Carbon::setTestNow($dueDate->clone()->addDays(12)); // D+12

        $action12d = new RunChargeRulerForNoLimitAction(['subscriber_id' => $subscriberId]);
        $action12d();

        $this->assertDatabaseHas('payments', [
            'subscriber_id' => $subscriberId,
            'status' => 'failed',
            'attempts' => 3,
        ]);

        Carbon::setTestNow($dueDate->clone()->addDays(20)); // D+20

        $action20d = new RunChargeRulerForNoLimitAction(['subscriber_id' => $subscriberId]);
        $action20d();

        $this->assertDatabaseHas('payments', [
            'subscriber_id' => $subscriberId,
            'status' => 'failed',
            'attempts' => 4,
        ]);

    }
}
