<?php

namespace Tests\Feature\Api\Checkout\Affiliate;

use App\PaymentPlanSplit;
use App\Producer;
use App\ProducerProduct;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\Traits\CreateSubscriberTrait;
use Tests\Feature\Traits\Database\ValidatesPixDatabase;
use Tests\Feature\Traits\Integration\MockPubSubTrait;
use Tests\Feature\Traits\Json\ValidatesPixJsonPayload;
use Tests\Feature\Traits\LocalDatabaseIds;
use Tests\TestCase;

/**
 * Test Single Sale via PIX
 */
class PixSaleAffiliateTest extends TestCase
{

    use MockPubSubTrait;
    use WithFaker;
    use CreateSubscriberTrait;
    use LocalDatabaseIds;
    use ValidatesPixJsonPayload;
    use ValidatesPixDatabase;

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

    public function test_pix_no_coupon_no_orderbump_one_installment()
    {
        $this->withoutMiddleware();

        $this->mockPubSubWithCount(1); // Whatsapp integration via Pub/Sub

        $producer = Producer::where('type', 'A')->first();
        $producer->producerProduct->each(fn(ProducerProduct $producerProduct) => $producerProduct->update([
            'status' => 'active',
        ]));

        $subscriberId = $this->createSubscriber($this->platformId, $this->salePlanId);

        $response = $this->postJson("/api/checkout/{$this->platformId}/{$this->salePlanId}", [
            "payment_method" => "pix",
            "subscriber_id" => $subscriberId,
            "platform_id" => $this->platformId,
            "plan_id" => $this->salePlanId, // 100
            "affiliate_id" => '2',
            "order_bump" => [],
        ]);

        if ($response->getStatusCode() == 409) {
            $this->markTestSkipped('Gateway problem');
        }

        $response->assertStatus(200);

        $this->assertJsonPayload($response);

        $orderCode = $response->json('*.order_code')[0] ?? null;

        // assert side effects
        $this->assertDatabasePix($subscriberId, $this->salePlanId, $orderCode, 100);

        $this->assertEquals(3, PaymentPlanSplit::where('order_code', $orderCode)->count()); // X, C, A
    }

}
