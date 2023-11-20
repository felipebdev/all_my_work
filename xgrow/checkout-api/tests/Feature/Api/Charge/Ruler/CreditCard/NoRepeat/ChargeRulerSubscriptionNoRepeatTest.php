<?php

namespace Tests\Feature\Api\Charge\Ruler\CreditCard\NoRepeat;

use App\Services\Actions\RunChargeRulerForSubscriptionsAction;
use App\Services\Actions\RunRegularChargeForSubscriptionAction;
use App\Services\Mundipagg\CreditCardRecurrenceService;
use Carbon\Carbon;
use Tests\Feature\Helper\MundipaggToken;
use Tests\Feature\Traits\CreateSubscriberTrait;
use Tests\Feature\Traits\Integration\MockPubSubTrait;
use Tests\Feature\Traits\LocalDatabaseIds;
use Tests\TestCase;

class ChargeRulerSubscriptionNoRepeatTest extends TestCase
{

    use MockPubSubTrait;
    use CreateSubscriberTrait;
    use LocalDatabaseIds;

    public function test_all_retries_fail()
    {
        $this->withoutMiddleware();

        $this->mockPubSubWithCount(2);

        $subscriberId = $this->createSubscriber($this->platformId, $this->subscriptionPlanId);

        $token = MundipaggToken::cardOk($this->faker->creditCardNumber('MasterCard'));

        $response = $this->postJson("/api/checkout/{$this->platformId}/{$this->subscriptionPlanId}", [
            "payment_method" => "credit_card",
            "subscriber_id" => $subscriberId,
            "platform_id" => $this->platformId,
            "plan_id" => $this->subscriptionPlanId, // 100
            "order_bump" => [],
            "cc_info" => [
                [
                    "token" => "$token",
                    "installment" => 12,
                    "value" => "100.00"
                ]
            ],
        ]);

        if ($response->getStatusCode() == 409) {
            $this->markTestSkipped('Gateway problem');
        }

        $response->assertStatus(200);

        $dueDate = Carbon::now()->addDays(30); // next recurrence: D

        // renew on due date

        Carbon::setTestNow($dueDate);

        CreditCardRecurrenceService::$forceFailStatusDebug = true;

        $regularCharge = new RunRegularChargeForSubscriptionAction(['subscriber_id' => $subscriberId]);
        $regularCharge();

        // assert failed on due date
        $this->assertDatabaseHas('payments', [
            'subscriber_id' => $subscriberId,
            'status' => 'failed',
            'attempts' => 1,
        ]);

        // 1st retry (valid charge)

        Carbon::setTestNow($dueDate->clone()->addDays(5)); // D+5

        /** @var RunChargeRulerForSubscriptionsAction $action5d */
        $action5d = new RunChargeRulerForSubscriptionsAction(['subscriber_id' => $subscriberId]);
        $action5d();

        // assert that second error was registered
        $this->assertDatabaseHas('payments', [
            'subscriber_id' => $subscriberId,
            'status' => 'failed',
            'attempts' => 2,
        ]);

        // wrong retry in same day

        /** @var RunChargeRulerForSubscriptionsAction $action5d */
        $action5d = new RunChargeRulerForSubscriptionsAction(['subscriber_id' => $subscriberId]);
        $action5d();

        // assert that "third" retry was not ran
        $this->assertDatabaseHas('payments', [
            'subscriber_id' => $subscriberId,
            'status' => 'failed',
            'attempts' => 2,
        ]);
    }

}
