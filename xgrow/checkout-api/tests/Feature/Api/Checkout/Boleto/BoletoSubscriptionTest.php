<?php

namespace Tests\Feature\Api\Checkout\Boleto;

use App\Payment;
use App\Subscriber;
use App\Subscription;
use Illuminate\Support\Facades\Queue;
use Modules\Integration\Queue\Jobs\HandleIntegration;
use Tests\Feature\Traits\CreateSubscriberTrait;
use Tests\Feature\Traits\Integration\MockPubSubTrait;
use Tests\Feature\Traits\Json\ValidatesBoletoJsonPayload;
use Tests\Feature\Traits\LocalDatabaseIds;
use Tests\TestCase;

/**
 * Test Subscription Sale via Boleto
 */
class BoletoSubscriptionTest extends TestCase
{

    use MockPubSubTrait;
    use CreateSubscriberTrait;
    use LocalDatabaseIds;
    use ValidatesBoletoJsonPayload;

    public function test_boleto_noCoupon_noOrderbump()
    {
        Queue::fake();
        //Mail::fake();

        $this->mockPubSubWithCount(2);

        $this->withoutExceptionHandling();

        $this->withoutMiddleware();

        $subscriberId = $this->createSubscriber($this->platformId, $this->subscriptionPlanId);

        $response = $this->postJson("/api/checkout/{$this->platformId}/{$this->subscriptionPlanId}", [
            "payment_method" => "boleto",
            "subscriber_id" => $subscriberId,
            "platform_id" => $this->platformId,
            "plan_id" => $this->subscriptionPlanId, // 100
            "order_bump" => []
        ]);

        if ($response->getStatusCode() == 409) {
            $this->markTestSkipped('Gateway problem');
        }

        $response->assertStatus(200);

        $this->assertJsonPayload($response);

        $orderCode = $response->json('*.order_code')[0] ?? null;

        $this->assertDatabaseBoleto($subscriberId, $this->subscriptionPlanId, $orderCode, 100);

        Queue::assertPushedOn('xgrow-jobs:integrations-checkout', HandleIntegration::class);

        //Mail::assertSent(SendMailAuto::class);
        //Mail::assertQueued(SendMailAuto::class);
    }


    public function test_boleto_withCoupon_withOrderbump()
    {
        $this->withoutMiddleware();

        $this->mockPubSubWithCount(1);

        $subscriberId = $this->createSubscriber($this->platformId, $this->subscriptionPlanId);

        $response = $this->postJson("/api/checkout/{$this->platformId}/{$this->subscriptionPlanId}", [
            'payment_method' => 'boleto',
            'subscriber_id' => $subscriberId,
            'platform_id' => $this->platformId,
            'plan_id' => $this->subscriptionPlanId, // R$100
            'cupom' => '20PORCENTO', //  -R$20
            'order_bump' => $this->orderBumps, // R$50
        ]);

        if ($response->getStatusCode() == 409) {
            $this->markTestSkipped('Gateway problem');
        }

        $response->assertStatus(200);

        $this->assertJsonPayload($response);

        $orderCode = $response->json('*.order_code')[0] ?? null;

        $this->assertDatabaseBoleto($subscriberId, $this->subscriptionPlanId, $orderCode, 100 - 20 + 50);
    }

    private function assertDatabaseBoleto(int $subscriberId, int $planId, $orderCode, $price): void
    {
        $this->assertDatabaseHas('subscribers', [
            'id' => $subscriberId,
            'status' => Subscriber::STATUS_LEAD,
        ]);

        $this->assertDatabaseHas('subscriptions', [
            'subscriber_id' => $subscriberId,
            'plan_id' => $planId,
            'gateway_transaction_id' => $orderCode,
            'status' => Subscription::STATUS_PENDING_PAYMENT,
        ]);

        $this->assertDatabaseHas('payments', [
            'order_code' => $orderCode,
            'status' => Payment::STATUS_PENDING,
            'price' => $price,
        ]);
    }


}
