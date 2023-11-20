<?php

namespace Tests\Feature\Api\Checkout\Pix;

use App\Http\Controllers\Webhooks\StatusPagarmeController;
use Illuminate\Support\Facades\Queue;
use Modules\Integration\Queue\Jobs\HandleIntegration;
use Tests\Feature\Traits\CreateSubscriberTrait;
use Tests\Feature\Traits\Integration\MockPubSubTrait;
use Tests\Feature\Traits\Json\ValidatesPixJsonPayload;
use Tests\Feature\Traits\LocalDatabaseIds;
use Tests\TestCase;

/**
 * Test Single Sale via PIX
 */
class PixSaleTest extends TestCase
{
    use MockPubSubTrait;
    use CreateSubscriberTrait;
    use LocalDatabaseIds;
    use ValidatesPixJsonPayload;

    //use RefreshDatabase;

    public function test_pix_with_coupon_with_orderbump()
    {
        Queue::fake();

        $this->mockPubSubWithCount(2); // Whatsapp integration via Pub/Sub: "Chave PIX" and Login/Password

        $this->withoutMiddleware();

        $subscriberId = $this->createSubscriber($this->platformId, $this->salePlanId);

        $response = $this->postJson("/api/checkout/{$this->platformId}/{$this->salePlanId}", [
            "payment_method" => "pix",
            "cupom" => "20PORCENTO",
            "subscriber_id" => $subscriberId,
            "platform_id" => $this->platformId,
            "plan_id" => $this->salePlanId,
            "order_bump" => $this->orderBumps
        ]);

        if ($response->getStatusCode() == 409) {
            $this->markTestSkipped('Gateway problem');
        }

        $response->assertStatus(200);

        $this->assertJsonPayload($response);

        // fake response

        $orderCodes = $response->json('*.order_code');

        StatusPagarmeController::$skipPostbackSignatureValidation = true;

        $response = $this->withHeaders(['X-Hub-Signature' => '123'])
            ->json('POST', '/api/checkout/order/paid/pagarme', [
                'id' => '523247310',
                'object' => 'transaction',
                'transaction' => [
                    'object' => 'transaction',
                    'status' => 'paid',
                    'tid' => $orderCodes[0],
                    'payment_method' => 'pix',
                    'metadata' => [
                        'antecipation_value' => '0',
                        'customer_value' => '93.5',
                        'plans_value' => '100',
                        'service_value' => '95.00',
                        'tax_value' => '5',
                    ],
                ],
            ]);

        Queue::assertPushedOn('xgrow-jobs:integrations-checkout', HandleIntegration::class);
    }


}
