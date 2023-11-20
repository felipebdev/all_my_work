<?php

namespace Tests\Feature\Api\Checkout\Coproducer;

use App\PaymentPlanSplit;
use App\Producer;
use App\ProducerProduct;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\Feature\Helper\MundipaggToken;
use Tests\Feature\Traits\CreateSubscriberTrait;
use Tests\Feature\Traits\Database\ValidatesCreditCardDatabase;
use Tests\Feature\Traits\Integration\MockPubSubTrait;
use Tests\Feature\Traits\Json\ValidatesCreditCardJsonPayload;
use Tests\Feature\Traits\LocalDatabaseIds;
use Tests\TestCase;

class CreditCardSaleWithCoproductionTest extends TestCase
{

    use MockPubSubTrait;
    use WithFaker;
    use CreateSubscriberTrait;
    use LocalDatabaseIds;
    use ValidatesCreditCardJsonPayload;
    use ValidatesCreditCardDatabase;

    //use DatabaseTransactions;
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

    public function test_one_coproducer_single_installment()
    {
        $this->withoutMiddleware();

        $this->mockPubSubWithCount(1);

        $producer = Producer::where('type', 'P')->first();
        $producer->producerProduct->each(fn(ProducerProduct $producerProduct) => $producerProduct->update([
            'status' => 'active',
        ]));

        $subscriberId = $this->createSubscriber($this->platformId, $this->salePlanId);

        $token = MundipaggToken::randomValidCvv($this->faker->creditCardNumber('MasterCard'));

        $response = $this->postJson("/api/checkout/{$this->platformId}/{$this->salePlanId}", [
            "payment_method" => "credit_card",
            "cc_info" => [
                [
                    "token" => "$token",
                    "installment" => 1,
                    "value" => "100.00"
                ]
            ],
            "subscriber_id" => $subscriberId,
            "platform_id" => $this->platformId,
            "plan_id" => $this->salePlanId,
            "order_bump" => [],
        ]);

        if ($response->getStatusCode() == 409) {
            $this->markTestSkipped('Gateway problem');
        }

        $response->assertStatus(200);

        $this->assertJsonPayload($response);

        $orderCode = $response->json('*.order_code')[0] ?? null;

        // assert side effects
        $this->assertDatabaseCreditCard($subscriberId, $this->salePlanId, $orderCode, 100);

        $this->assertEquals(3,  PaymentPlanSplit::where('order_code', $orderCode)->count()); // X, C, P
    }

}
