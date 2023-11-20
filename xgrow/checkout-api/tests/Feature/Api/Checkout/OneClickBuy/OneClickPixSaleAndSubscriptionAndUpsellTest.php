<?php

namespace Tests\Feature\Api\Checkout\OneClickBuy;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\Traits\CreateSubscriberTrait;
use Tests\Feature\Traits\Integration\MockPubSubTrait;
use Tests\Feature\Traits\Json\ValidatesPixJsonPayload;
use Tests\Feature\Traits\LocalDatabaseIds;
use Tests\TestCase;

class OneClickPixSaleAndSubscriptionAndUpsellTest extends TestCase
{

    use MockPubSubTrait;
    use WithFaker;
    use CreateSubscriberTrait;
    use LocalDatabaseIds;
    use ValidatesPixJsonPayload;

    //use RefreshDatabase;

    public function test_boleto_sale_and_one_click_subscription_and_one_click_upsell()
    {
        $this->withoutMiddleware();

        $this->mockPubSubWithCount(1);

        $subscriberId = $this->createSubscriber($this->platformId, $this->salePlanId);

        $checkoutResponse = $this->postJson("/api/checkout/{$this->platformId}/{$this->salePlanId}", [
            'payment_method' => 'pix',
            'subscriber_id' => $subscriberId,
            'platform_id' => $this->platformId,
            'plan_id' => $this->salePlanId,
            'order_bump' => []
        ]);

        $checkoutResponse->assertStatus(200);

        $this->assertJsonPayload($checkoutResponse);

        // first one click

        $checkoutJson = $checkoutResponse->json();

        $firstOneClickId = $checkoutJson[0]['one_click'] ?? null;

        $firstOneClickResponse = $this->postJson("/api/checkout/{$this->platformId}/{$this->subscriptionPlanId}/{$firstOneClickId}");

        $firstOneClickResponse->assertStatus(200);

        $this->assertJsonPayload($firstOneClickResponse);

        // second one click

        $firstOneClickJson = $firstOneClickResponse->json();

        $secondOneClickId = $firstOneClickJson[0]['one_click'] ?? null;

        $secondOneClickResponse = $this->postJson("/api/checkout/{$this->platformId}/{$this->upsell}/{$secondOneClickId}");

        $secondOneClickResponse->assertStatus(200);

        $this->assertJsonPayload($secondOneClickResponse);
    }

}
