<?php

namespace Tests\Feature\Api\Checkout\CreditCard;

use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Feature\Helper\MundipaggToken;
use Tests\Feature\Traits\CreateSubscriberTrait;
use Tests\Feature\Traits\Database\ValidatesCreditCardDatabase;
use Tests\Feature\Traits\Integration\MockPubSubTrait;
use Tests\Feature\Traits\Json\ValidatesCreditCardJsonPayload;
use Tests\Feature\Traits\LocalDatabaseIds;
use Tests\TestCase;

/**
 * Test Subscription Sale via Credit Card
 */
class CreditCardSubscriptionTest extends TestCase
{

    use MockPubSubTrait;
    use CreateSubscriberTrait;
    use LocalDatabaseIds;
    use ValidatesCreditCardJsonPayload;
    use ValidatesCreditCardDatabase;

    //use RefreshDatabase;

    public function test_subscription_with_coupon_with_order_bump()
    {
        $this->withoutMiddleware();

        $this->mockPubSubWithCount(1);

        $subscriberId = $this->createSubscriber($this->platformId, $this->subscriptionPlanId);

        $token = MundipaggToken::cardOk($this->faker->creditCardNumber('MasterCard'));

        $response = $this->postJson("/api/checkout/{$this->platformId}/{$this->subscriptionPlanId}", [
            "payment_method" => "credit_card",
            "subscriber_id" => $subscriberId,
            "platform_id" => $this->platformId,
            "plan_id" => $this->subscriptionPlanId, // 100
            "cupom" => "10REAIS", // -10
            "order_bump" => $this->orderBumps, // 50 - 10%
            "cc_info" => [
                [
                    "token" => "$token",
                    "installment" => 12,
                    "value" => "140.00"
                ]
            ],
        ]);


        if ($response->getStatusCode() == 409) {
            $this->markTestSkipped('Gateway problem');
        }

        $response->assertStatus(200);

        $this->assertJsonPayload($response);

        $orderCode = $response->json('*.order_code')[0] ?? null;

        // assert side effects
        $this->assertDatabaseCreditCard($subscriberId, $this->subscriptionPlanId, $orderCode, 168);
    }

    public function test_invalid_cvv_subscription()
    {
        $this->withoutMiddleware();

        $this->mockPubSubWithCount(1);

        $subscriberId = $this->createSubscriber($this->platformId, $this->salePlanId);

        $token = MundipaggToken::randomInvalid($this->faker->creditCardNumber('MasterCard'));

        $response = $this->postJson("/api/checkout/{$this->platformId}/{$this->salePlanId}", [
            "payment_method" => "credit_card",
            "cc_info" => [
                [
                    "token" => "$token",
                    "installment" => 10,
                    "value" => "100.00"
                ]
            ],
            "subscriber_id" => $subscriberId,
            "platform_id" => $this->platformId,
            "plan_id" => $this->salePlanId,
            "order_bump" => []
        ]);

        $response->assertStatus(400);

        $response->assertJson(function (AssertableJson $json) {
            $json->whereType('message', 'string');
            $json->whereType('failures', 'array');
            $json->whereType('failures.0.last_four_digits', 'string');
            $json->whereType('failures.0.brand', 'string');
            $json->whereType('failures.0.code', 'string');
            $json->whereType('failures.0.message', 'string');
            $json->whereType('failures.0.friendly_message', 'string');
        });
    }

}
