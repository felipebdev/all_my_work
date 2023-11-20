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
 * Test Single Sale via Boleto
 */
class BoletoSaleTest extends TestCase
{
    use MockPubSubTrait;
    use CreateSubscriberTrait;
    use LocalDatabaseIds;
    use ValidatesBoletoJsonPayload;

    public function test_boleto_without_coupon()
    {
        Queue::fake();

        $this->mockPubSubWithCount(1); // Whatsapp integration via Pub/Sub

        $this->withoutMiddleware();

        $subscriberId = $this->createSubscriber($this->platformId, $this->salePlanId);

        $response = $this->postJson("/api/checkout/{$this->platformId}/{$this->salePlanId}", [
            "payment_method" => "boleto",
            "subscriber_id" => $subscriberId,
            "platform_id" => $this->platformId,
            "plan_id" => $this->salePlanId, // 100
            "order_bump" => []
        ]);

        if ($response->getStatusCode() == 409) {
            $this->markTestSkipped('Gateway problem');
        }

        $response->assertStatus(200);

        $this->assertJsonPayload($response);

        $orderCode = $response->json('*.order_code')[0] ?? null;

        $this->assertDatabaseHas('subscribers', [
            'id' => $subscriberId,
            'status' => Subscriber::STATUS_LEAD,
        ]);

        $this->assertDatabaseHas('subscriptions', [
            'subscriber_id' => $subscriberId,
            'plan_id' => $this->salePlanId,
            'gateway_transaction_id' => $orderCode,
            'status' => Subscription::STATUS_PENDING_PAYMENT,
        ]);

        $this->assertDatabaseHas('payments', [
            'order_code' => $orderCode,
            'status' => Payment::STATUS_PENDING,
            'price' => 100,
        ]);

        Queue::assertPushedOn('xgrow-jobs:integrations-checkout', HandleIntegration::class);
    }

}
