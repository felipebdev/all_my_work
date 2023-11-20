<?php

namespace Tests\Feature\Api\Checkout\CreditCard;

use App\Payment;
use Tests\Feature\Helper\MundipaggToken;
use Tests\Feature\Traits\CreateSubscriberTrait;
use Tests\Feature\Traits\Database\ValidatesCreditCardDatabase;
use Tests\Feature\Traits\Integration\MockPubSubTrait;
use Tests\Feature\Traits\Json\ValidatesCreditCardJsonPayload;
use Tests\Feature\Traits\LocalDatabaseIds;
use Tests\TestCase;

/**
 * Test Subscription Sale via Credit Card using installments
 */
class CreditCardSubscriptionWithInstallmentsTest extends TestCase
{

    use MockPubSubTrait;
    use CreateSubscriberTrait;
    use LocalDatabaseIds;
    use ValidatesCreditCardJsonPayload;
    use ValidatesCreditCardDatabase;

    //use RefreshDatabase;

    public function test_subscription_without_coupon_without_order_bump()
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
            "order_bump" => [], // 50 - 10%
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

        $this->assertJsonPayload($response);

        $orderCode = $response->json('*.order_code')[0] ?? null;

        // assert side effects
        $this->assertDatabaseCreditCard($subscriberId, $this->subscriptionPlanId, $orderCode, 120);

        $payment = Payment::where('order_code', $orderCode)->first();

        $this->assertDatabaseHas('recurrences', [
            'order_number' => $payment->order_number,
            'payment_method' => 'credit_card',
            'type' => 'S',
            'default_installments' => 12,
            'current_charge' => 1,
        ]);

        $this->assertEquals($payment->price, $payment->customer_value + $payment->service_value);
    }


}
