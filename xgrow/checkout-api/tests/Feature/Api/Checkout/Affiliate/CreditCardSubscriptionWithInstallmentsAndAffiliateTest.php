<?php

namespace Tests\Feature\Api\Checkout\Affiliate;

use App\Payment;
use App\PaymentPlanSplit;
use App\Producer;
use App\ProducerProduct;
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
class CreditCardSubscriptionWithInstallmentsAndAffiliateTest extends TestCase
{

    use MockPubSubTrait;
    use CreateSubscriberTrait;
    use LocalDatabaseIds;
    use ValidatesCreditCardJsonPayload;
    use ValidatesCreditCardDatabase;

    //use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        ProducerProduct::query()->update(['status' => 'pending']); // disable all producers/affiliates
    }

    protected function tearDown(): void
    {
        ProducerProduct::query()->update(['status' => 'pending']); // re-disable all producers/affiliates

        parent::tearDown();
    }

    public function test_subscription_with_coupon()
    {
        $this->withoutMiddleware();

        $this->mockPubSubWithCount(1);

        $producer = Producer::where('type', 'A')->first();
        $producer->producerProduct->each(fn(ProducerProduct $producerProduct) => $producerProduct->update([
            'status' => 'active',
        ]));

        $subscriberId = $this->createSubscriber($this->platformId, $this->subscriptionPlanId);

        $token = MundipaggToken::cardOk($this->faker->creditCardNumber('MasterCard'));

        $response = $this->postJson("/api/checkout/{$this->platformId}/{$this->subscriptionPlanId}", [
            "payment_method" => "credit_card",
            "subscriber_id" => $subscriberId,
            "platform_id" => $this->platformId,
            "plan_id" => $this->subscriptionPlanId, // 100
            "affiliate_id" => 2,
            "cupom" => "10REAIS", // -10
            "order_bump" => [],
            "cc_info" => [
                [
                    "token" => "$token",
                    "installment" => 12,
                    "value" => "90.00"
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
        $this->assertDatabaseCreditCard($subscriberId, $this->subscriptionPlanId, $orderCode, 108);

        $payment = Payment::where('order_code', $orderCode)->first();

        $this->assertDatabaseHas('recurrences', [
            'order_number' => $payment->order_number,
            'payment_method' => 'credit_card',
            'type' => 'S',
            'default_installments' => 12,
            'affiliate_id' => 2,
            'current_charge' => 1,
        ]);

        $this->assertEquals(3,  PaymentPlanSplit::where('order_code', $orderCode)->count()); // X, C, A
    }

}
