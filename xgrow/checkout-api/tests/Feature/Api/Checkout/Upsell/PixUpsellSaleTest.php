<?php

namespace Tests\Feature\Api\Checkout\Upsell;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\Helper\MundipaggToken;
use Tests\Feature\Traits\CreateSubscriberTrait;
use Tests\Feature\Traits\Integration\MockPubSubTrait;
use Tests\Feature\Traits\Json\ValidatesPixJsonPayload;
use Tests\Feature\Traits\LocalDatabaseIds;
use Tests\TestCase;

class PixUpsellSaleTest extends TestCase
{

    use MockPubSubTrait;
    use WithFaker;
    use CreateSubscriberTrait;
    use LocalDatabaseIds;
    use ValidatesPixJsonPayload;

    //use RefreshDatabase;

    public function test_credit_card_sale_and_pix_upsell()
    {
        $creditCardNumber = $this->faker->creditCardNumber('MasterCard');
        $lastFourDigits = substr($creditCardNumber, -4);
        $token = MundipaggToken::cardOk($creditCardNumber);

        $this->withoutMiddleware();

        $this->mockPubSubWithCount(1);

        $subscriberId = $this->createSubscriber($this->platformId, $this->salePlanId);

        //dump('------------------------');
        //dump('Starting main checkout');
        //dump('------------------------');

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
            "order_bump" => []
        ]);

        //dump('------------------------');
        //dump('Starting Upsell checkout');
        //dump('------------------------');

        $upsellResponse = $this->postJson("/api/checkout/upsell/{$this->platformId}", [
            "payment_method" => "pix",
            "plan_id" => $this->salePlanId,
            "platform_id" => $this->platformId,
            "subscriber_id" => $subscriberId,
        ]);

        $upsellResponse->assertStatus(200);
        $this->assertJsonPayload($upsellResponse);
    }

}
