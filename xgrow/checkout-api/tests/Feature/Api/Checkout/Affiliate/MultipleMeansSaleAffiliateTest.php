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
 * Test Multiple Credit Cards Sale Test
 */
class MultipleMeansSaleAffiliateTest extends TestCase
{

    use MockPubSubTrait;
    use WithFaker;
    use CreateSubscriberTrait;
    use LocalDatabaseIds;
    use ValidatesCreditCardJsonPayload;
    use ValidatesCreditCardDatabase;

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

    public function test_two_cards_without_coupon_with_orderBump_single_installment_with_affiliate()
    {
        $this->withoutMiddleware();

        $this->mockPubSubWithCount(1);

        $producer = Producer::where('type', 'A')->first();
        $producer->producerProduct->each(fn(ProducerProduct $producerProduct) => $producerProduct->update([
            'status' => 'active',
        ]));

        $subscriberId = $this->createSubscriber($this->platformId, $this->salePlanId);

        $token1 = MundipaggToken::randomValidCvv($this->faker->creditCardNumber('MasterCard'));
        $token2 = MundipaggToken::randomValidCvv($this->faker->creditCardNumber('Visa'));

        $response = $this->postJson("/api/checkout/{$this->platformId}/{$this->salePlanId}", [
            "payment_method" => "credit_card",
            "cc_info" => [
                [
                    "token" => "$token1",
                    "installment" => 1,
                    "value" => "90.00"
                ],
                [
                    "token" => "$token2",
                    "installment" => 1,
                    "value" => "60.00"
                ]
            ],
            "subscriber_id" => $subscriberId,
            "platform_id" => $this->platformId,
            "plan_id" => $this->salePlanId,
            "order_bump" => $this->orderBumps,
            "affiliate_id" => '2',
        ]);

        if ($response->getStatusCode() == 409) {
            $this->markTestSkipped('Gateway problem');
        }

        $response->assertStatus(200);

        $this->assertJsonPayload($response);

        $orderCode = $response->json('*.order_code')[0] ?? null;

        $this->assertDatabaseCreditCard($subscriberId, $this->salePlanId, $orderCode, 90);
        $this->assertDatabaseCreditCard($subscriberId, $this->salePlanId, $orderCode, 60);

        // main product: X, C, A (x2)
        // order bump: X, C (x2)
        $this->assertEquals(10,  PaymentPlanSplit::where('order_code', $orderCode)->count());
    }

}
