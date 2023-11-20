<?php

namespace Tests\Feature\Api\Checkout\Affiliate;

use App\PaymentPlanSplit;
use App\Producer;
use App\ProducerProduct;
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
class CreditCardSaleAffiliateTest extends TestCase
{

    use MockPubSubTrait;
    use WithFaker;
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

    public function test_creditcard_no_coupon_no_orderbump_one_installment()
    {
        $this->withoutMiddleware();

        $this->mockPubSubWithCount(1);

        $producer = Producer::where('type', 'A')->first();
        $producer->producerProduct->each(fn(ProducerProduct $producerProduct) => $producerProduct->update([
            'status' => 'active',
        ]));

        $subscriberId = $this->createSubscriber($this->platformId, $this->salePlanId);

        $token = MundipaggToken::randomValidCvv($this->faker->creditCardNumber('MasterCard'));

        $response = $this->postJson("/api/checkout/{$this->platformId}/{$this->salePlanId}", [
            "payment_method" => "credit_card",
            "subscriber_id" => $subscriberId,
            "platform_id" => $this->platformId,
            "plan_id" => $this->salePlanId, // 100
            "affiliate_id" => '2',
            "order_bump" => [],
            "cc_info" => [
                [
                    "token" => "$token",
                    "installment" => 1,
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
        $this->assertDatabaseCreditCard($subscriberId, $this->salePlanId, $orderCode, 100);

        $this->assertEquals(3,  PaymentPlanSplit::where('order_code', $orderCode)->count()); // X, C, A
    }

    public function test_creditcard_with_coupon_with_orderbump_six_installments()
    {
        $this->withoutMiddleware();

        $this->mockPubSubWithCount(1);

        $producer = Producer::where('type', 'A')->first();
        $producer->producerProduct->each(fn(ProducerProduct $producerProduct) => $producerProduct->update([
            'status' => 'active',
        ]));

        $subscriberId = $this->createSubscriber($this->platformId, $this->salePlanId);

        $token = MundipaggToken::cardOk($this->faker->creditCardNumber('MasterCard'));

        $response = $this->postJson("/api/checkout/{$this->platformId}/{$this->salePlanId}", [
            "payment_method" => "credit_card",
            "subscriber_id" => $subscriberId,
            "platform_id" => $this->platformId,
            "plan_id" => $this->salePlanId, // 100
            "affiliate_id" => '2',
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

        // X, C, A (main product) + X, C (order bump)
        $this->assertEquals(5,  PaymentPlanSplit::where('order_code', $orderCode)->count());

        // SX-616
        $this->assertDatabaseHas('payment_plan', [
            'type' => 'product',
            'customer_value' => 74.5,
        ]);
        $this->assertDatabaseHas('payment_plan_split', [
            'type' => PaymentPlanSplit::SPLIT_TYPE_AFFILIATE,
            'percent' => 10,
            'value' => 7.45, // 10% of 74.5 on main product
        ]);

    }
}
