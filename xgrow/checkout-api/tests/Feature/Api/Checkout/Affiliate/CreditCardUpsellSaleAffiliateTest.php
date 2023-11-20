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

class CreditCardUpsellSaleAffiliateTest extends TestCase
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

    public function test_credit_card_upsell_with_affiliate()
    {
        $this->withoutMiddleware();

        $this->mockPubSubWithCount(1);

        $producer = Producer::where('type', 'A')->first();
        $producer->producerProduct->each(fn(ProducerProduct $producerProduct) => $producerProduct->update([
            'status' => 'active',
        ]));

        $originalNumberOfPaymentPlanSplitRecords = PaymentPlanSplit::count();

        $creditCardNumber = $this->faker->creditCardNumber('MasterCard');
        $lastFourDigits = substr($creditCardNumber, -4);
        $token = MundipaggToken::cardOk($creditCardNumber);

        $subscriberId = $this->createSubscriber($this->platformId, $this->salePlanId);

        $mainResponse = $this->postJson("/api/checkout/{$this->platformId}/{$this->salePlanId}", [
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
            "affiliate_id" => '2',
        ]);

        $mainResponse->assertStatus(200);

        $upsellResponse = $this->postJson("/api/checkout/upsell/{$this->platformId}", [
            "payment_method" => "credit_card",
            "plan_id" => $this->salePlanId,
            "cc_info" => [
                [
                    "installment" => "1",
                    "value" => "50.00",
                    "brand" => "Mastercard",
                    "last_four_digits" => $lastFourDigits
                ]
            ],
            "platform_id" => $this->platformId,
            "subscriber_id" => $subscriberId,
            "affiliate_id" => '2',
        ]);

        $upsellResponse->assertStatus(200);

        $this->assertJsonPayload($upsellResponse);

        // X, C, A (Main Product) + X, C, A (Upsell)
        $this->assertDatabaseCount('payment_plan_split', $originalNumberOfPaymentPlanSplitRecords + 6);
    }

}
