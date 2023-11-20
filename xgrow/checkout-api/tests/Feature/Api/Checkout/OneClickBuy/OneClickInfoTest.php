<?php

namespace Tests\Feature\Api\Checkout\OneClickBuy;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\Helper\MundipaggToken;
use Tests\Feature\Traits\CreateSubscriberTrait;
use Tests\Feature\Traits\Integration\MockPubSubTrait;
use Tests\Feature\Traits\LocalDatabaseIds;
use Tests\TestCase;


class OneClickInfoTest extends TestCase
{

    use MockPubSubTrait;
    use WithFaker;
    use CreateSubscriberTrait;
    use LocalDatabaseIds;

    //use RefreshDatabase;

    public function test_creditcard_no_coupon_no_orderbump_one_installment()
    {
        $this->withoutMiddleware();

        $this->mockPubSubWithCount(1);

        $subscriberId = $this->createSubscriber($this->platformId, $this->salePlanId);

        $creditCardNumber = $this->faker->creditCardNumber('MasterCard');
        $lastFourDigits = substr($creditCardNumber, -4);
        $token = MundipaggToken::cardOk($creditCardNumber);

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
            'client_ip_address' => $clientIp = $this->faker->ipv4,
            'client_user_agent' => $userAgent = $this->faker->userAgent,
        ]);

        if ($response->getStatusCode() == 409) {
            $this->markTestSkipped('Gateway problem');
        }

        $response->assertStatus(200);

        $json = $response->json();

        $oneClickId = $json[0]['one_click'] ?? null;

        // 'checkout.oneclick.get'
        $response = $this->get("/api/checkout/platforms/{$this->platformId}/plans/{$this->subscriptionPlanId}/hash/{$oneClickId}");

        $response->assertJsonStructure([
            "plan_name",
            "plan_value",
            "payment_method",
        ]);
    }

}
