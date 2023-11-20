<?php

namespace Tests\Feature\Api\Checkout\CreditCard;

use App\Payment;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\Helper\MundipaggToken;
use Tests\Feature\Traits\CreateSubscriberTrait;
use Tests\Feature\Traits\Database\ValidatesCreditCardDatabase;
use Tests\Feature\Traits\Integration\MockPubSubTrait;
use Tests\Feature\Traits\Json\ValidatesCreditCardJsonPayload;
use Tests\Feature\Traits\LocalDatabaseIds;
use Tests\TestCase;

/**
 * Test Single Sale via Credit Card
 */
class CreditCardSaleTest extends TestCase
{

    use MockPubSubTrait;
    use WithFaker;
    use CreateSubscriberTrait;
    use LocalDatabaseIds;
    use ValidatesCreditCardJsonPayload;
    use ValidatesCreditCardDatabase;

    //use RefreshDatabase;

    public function test_credit_card_no_coupon_no_orderbump_two_installments()
    {
        $this->withoutMiddleware();

        $this->mockPubSubWithCount(1); // Whatsapp integration via Pub/Sub

        $subscriberId = $this->createSubscriber($this->platformId, $this->salePlanId);

        $token = MundipaggToken::cardOk($this->faker->creditCardNumber('MasterCard'));

        $response = $this->postJson("/api/checkout/{$this->platformId}/{$this->salePlanId}", [
            "payment_method" => "credit_card",
            "subscriber_id" => $subscriberId,
            "platform_id" => $this->platformId,
            "plan_id" => $this->salePlanId, // 100
            "order_bump" => [],
            "cc_info" => [
                [
                    "token" => "$token",
                    "installment" => 2,
                    "value" => "100.00"
                ]
            ],
        ]);

        if ($response->getStatusCode() == 409) {
            $this->markTestSkipped('Gateway problem');
        }

        if ($response->getStatusCode() == 409) {
            $this->markTestSkipped('Gateway problem');
        }

        $response->assertStatus(200);

        $this->assertJsonPayload($response);

        $orderCode = $response->json('*.order_code')[0] ?? null;

        // assert side effects

        $this->assertDatabaseHas('subscribers', [
            'id' => $subscriberId,
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('subscriptions', [
            'subscriber_id' => $subscriberId,
            'plan_id' => $this->salePlanId,
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('payments', [
            'order_code' => $orderCode,
            'status' => Payment::STATUS_PAID,
            'price' => 104.4,
            'plans_value' => 100,
            'tax_value' => 6.5,
        ]);

        $this->assertDatabaseCreditCard($subscriberId, $this->salePlanId, $orderCode, 104.4);
    }

    public function test_creditcard_with_coupon_with_orderbump_six_installments()
    {
        $this->withoutMiddleware();

        $this->mockPubSubWithCount(1);

        $subscriberId = $this->createSubscriber($this->platformId, $this->salePlanId);

        $token = MundipaggToken::cardOk($this->faker->creditCardNumber('MasterCard'));

        $response = $this->postJson("/api/checkout/{$this->platformId}/{$this->salePlanId}", [
            "payment_method" => "credit_card",
            "subscriber_id" => $subscriberId,
            "platform_id" => $this->platformId,
            "plan_id" => $this->salePlanId, // 100
            "cupom" => "20PORCENTO", // -20%
            "order_bump" => $this->orderBumps, // 50
            "cc_info" => [
                [
                    "token" => "$token",
                    "installment" => 6,
                    "value" => "130.00"
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
        $this->assertDatabaseCreditCard($subscriberId, $this->salePlanId, $orderCode, 143.64);
    }
}
