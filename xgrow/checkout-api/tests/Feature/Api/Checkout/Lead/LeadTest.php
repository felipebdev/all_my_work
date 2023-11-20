<?php

namespace Tests\Feature\Api\Checkout\Lead;

use App\Lead;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\Helper\MundipaggToken;
use Tests\Feature\Traits\CreateSubscriberTrait;
use Tests\Feature\Traits\Integration\MockPubSubTrait;
use Tests\Feature\Traits\LocalDatabaseIds;
use Tests\TestCase;

class LeadTest extends TestCase
{

    use MockPubSubTrait;
    use withFaker;
    use LocalDatabaseIds;
    use CreateSubscriberTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->faker = $this->makeFaker('pt_BR');
    }

    public function test_lead_product_via_credit_card()
    {
        $this->withoutMiddleware();

        $this->mockPubSubWithCount(1);

        // create subscriber
        $subscriberId = $this->createSubscriber($this->platformId, $this->salePlanId);

        // assert lead already has been created
        $this->assertDatabaseHas('leads', [
            'subscriber_id' => $subscriberId,
            'plan_id' => $this->salePlanId,
            'cart_status' => Lead::CART_STATUS_INITIATED,
        ]);

        $token = MundipaggToken::randomValidCvv($this->faker->creditCardNumber('MasterCard'));

        // order
        $this->postJson("/api/checkout/{$this->platformId}/{$this->salePlanId}", [
            "payment_method" => "credit_card",
            "subscriber_id" => $subscriberId,
            "platform_id" => $this->platformId,
            "plan_id" => $this->salePlanId,
            "order_bump" => [],
            "cc_info" => [
                [
                    "token" => "$token",
                    "installment" => 1,
                    "value" => "100.00"
                ]
            ],
        ]);

        // assert that credit card was charged
        $this->assertDatabaseHas('leads', [
            'subscriber_id' => $subscriberId,
            'plan_id' => $this->salePlanId,
            'cart_status' => Lead::CART_STATUS_CONFIRMED,
        ]);
    }

    public function test_lead_product_via_boleto()
    {
        $this->withoutMiddleware();

        $this->mockPubSubWithCount(1);

        // create subscriber
        $subscriberId = $this->createSubscriber($this->platformId, $this->salePlanId);

        // assert lead already has been created
        $this->assertDatabaseHas('leads', [
            'subscriber_id' => $subscriberId,
            'plan_id' => $this->salePlanId,
            'cart_status' => Lead::CART_STATUS_INITIATED,
        ]);

        // order by boleto
        $this->postJson("/api/checkout/{$this->platformId}/{$this->salePlanId}", [
            "payment_method" => "boleto",
            "subscriber_id" => $subscriberId,
            "platform_id" => $this->platformId,
            "plan_id" => $this->salePlanId,
        ]);

        // assert that lead has ordered (but payment is not confirmed)
        $this->assertDatabaseHas('leads', [
            'subscriber_id' => $subscriberId,
            'plan_id' => $this->salePlanId,
            'cart_status' => Lead::CART_STATUS_ORDERED,
        ]);
    }

    public function test_lead_order_bump()
    {
        $this->withoutMiddleware();

        $subscriberId = $this->createSubscriber($this->platformId, $this->salePlanId);

        // assert "order_bump" Lead

        $orderBumpId = $this->orderBumps[0];
        $this->postJson("/api/checkout/lead", [
            'platform_id' => $this->platformId,
            'subscriber_id' => $subscriberId,
            'type' => Lead::TYPE_ORDER_BUMP,
            'plan_id' => $orderBumpId,
        ]);

        $this->assertDatabaseHas('leads', [
            'subscriber_id' => $subscriberId,
            'plan_id' => $orderBumpId,
            'cart_status' => Lead::CART_STATUS_INITIATED,
            'type' => Lead::TYPE_ORDER_BUMP,
        ]);
    }

    public function test_lead_upsell()
    {
        $this->withoutMiddleware();

        $subscriberId = $this->createSubscriber($this->platformId, $this->salePlanId);

        // assert "upsell" Lead

        $this->postJson("/api/checkout/lead", [
            'platform_id' => $this->platformId,
            'subscriber_id' => $subscriberId,
            'type' => Lead::TYPE_UPSELL,
            'plan_id' => $this->upsell,
        ]);

        $this->assertDatabaseHas('leads', [
            'subscriber_id' => $subscriberId,
            'plan_id' => $this->upsell,
            'cart_status' => Lead::CART_STATUS_INITIATED,
            'type' => Lead::TYPE_UPSELL,
        ]);
    }

    public function test_lead_product_and_order_bump_via_credit_card()
    {
        $this->withoutMiddleware();

        $this->mockPubSubWithCount(1);

        // create subscriber
        $subscriberId = $this->createSubscriber($this->platformId, $this->salePlanId);

        // assert "product" Lead

        $this->assertDatabaseHas('leads', [
            'subscriber_id' => $subscriberId,
            'plan_id' => $this->salePlanId,
            'cart_status' => Lead::CART_STATUS_INITIATED,
            'type' => Lead::TYPE_PRODUCT,
        ]);


        // assert "order_bump" Lead intention

        $orderBumpId = $this->orderBumps[0];
        $this->postJson("/api/checkout/lead", [
            'platform_id' => $this->platformId,
            'subscriber_id' => $subscriberId,
            'type' => Lead::TYPE_ORDER_BUMP,
            'plan_id' => $orderBumpId,
        ]);

        $this->assertDatabaseHas('leads', [
            'subscriber_id' => $subscriberId,
            'plan_id' => $orderBumpId,
            'cart_status' => Lead::CART_STATUS_INITIATED,
            'type' => Lead::TYPE_ORDER_BUMP,
        ]);

        // Product + Order Bump via credit card

        $token = MundipaggToken::randomValidCvv($this->faker->creditCardNumber('MasterCard'));

        $this->postJson("/api/checkout/{$this->platformId}/{$this->salePlanId}", [
            "payment_method" => "credit_card",
            "subscriber_id" => $subscriberId,
            "platform_id" => $this->platformId,
            "plan_id" => $this->salePlanId,
            "order_bump" => $this->orderBumps,
            "cc_info" => [
                [
                    "token" => "$token",
                    "installment" => 1,
                    "value" => "150.00"
                ]
            ],
        ]);

        // Assert cart status is CONFIRMED

        $this->postJson("/api/checkout/lead", [
            'platform_id' => $this->platformId,
            'subscriber_id' => $subscriberId,
            'cart_status' => Lead::CART_STATUS_CONFIRMED,
            'type' => Lead::TYPE_ORDER_BUMP,
            'plan_id' => $orderBumpId,
        ]);

        $this->assertDatabaseHas('leads', [
            'subscriber_id' => $subscriberId,
            'plan_id' => $orderBumpId,
            'cart_status' => Lead::CART_STATUS_CONFIRMED,
            'type' => Lead::TYPE_ORDER_BUMP,
        ]);
    }

}
