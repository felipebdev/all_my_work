<?php

namespace Tests\Regression\Api\Charge;

use App\Payment;
use App\Producer;
use App\ProducerProduct;
use App\Services\Actions\RunRegularChargeForNoLimitAction;
use App\Services\MundipaggService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithFaker;
use MundiAPILib\Models\GetSplitResponse;
use Tests\Feature\Helper\MundipaggToken;
use Tests\Feature\Traits\CreateSubscriberTrait;
use Tests\Feature\Traits\Integration\MockPubSubTrait;
use Tests\Feature\Traits\Json\ValidatesCreditCardJsonPayload;
use Tests\Feature\Traits\LocalDatabaseIds;
use Tests\TestCase;

use function collect;

/**
 * Test gateway's values on "Sem Limite" with coproducer
 */
class Xpp729NolimitSaleCoproducerTest extends TestCase
{

    use MockPubSubTrait;
    use WithFaker;
    use CreateSubscriberTrait;
    use LocalDatabaseIds;
    use ValidatesCreditCardJsonPayload;

    //use RefreshDatabase;

    public function test_no_limit_sale_with_coupon_and_orderbump()
    {
        $producer = Producer::where('type', 'P')->first();
        $producer->producerProduct->each(fn(ProducerProduct $producerProduct) => $producerProduct->update([
            'status' => 'active',
        ]));

        $this->withoutMiddleware();

        $this->mockPubSubWithCount(1);

        $subscriberId = $this->createSubscriber($this->platformId, $this->salePlanId);

        $token = MundipaggToken::insufficientBalance($this->faker->creditCardNumber('MasterCard'));

        $response = $this->postJson("/api/checkout/{$this->platformId}/{$this->salePlanId}", [
            'payment_method' => "credit_card",
            'cc_info' => [
                [
                    'token' => "$token",
                    'installment' => 2,
                    'value' => '100.00',
                ]
            ],
            'subscriber_id' => $subscriberId,
            'platform_id' => $this->platformId,
            'plan_id' => $this->salePlanId, // 100
        ]);

        //dump($response->json());

        if ($response->getStatusCode() == 409) {
            $this->markTestSkipped('Gateway problem');
        }

        $response->assertStatus(200);

        $this->assertJsonPayload($response);

        // first payment

        $firstPaid = Payment::query()
            ->where('status', Payment::STATUS_PAID)
            ->latest('id')
            ->first();

        $this->assertGatewayValues($firstPaid->order_id, 920, 3680, 620);

        // second payment (renew)

        $dueDate = Carbon::now()->addDays(30); // next recurrence: D

        Carbon::setTestNow($dueDate);

        $action = new RunRegularChargeForNoLimitAction(['subscriber_id' => $subscriberId]);
        $action();

        $secondPaid = Payment::query()
            ->where('status', Payment::STATUS_PAID)
            ->latest('id')
            ->first();

        $this->assertNotEquals($firstPaid->order_id, $secondPaid->order_id);

        $this->assertGatewayValues($secondPaid->order_id, 920, 3680, 620);
    }

    private function assertGatewayValues(
        string $orderId,
        int $producerAmount,
        int $clientAmount,
        int $xgrowAmount
    ): void {
        $mundipaggService = new MundipaggService();

        $orderResponses = $mundipaggService->getClient()->getOrders()->getOrder($orderId);

        $charges = collect($orderResponses->charges);

        /** @var \MundiAPILib\Models\GetChargeResponse $getChargeResponse */
        $getChargeResponse = $charges->first();

        $splits = collect($getChargeResponse->lastTransaction->split);

        /** @var GetSplitResponse $coproducerSplitResponse */
        $coproducerSplitResponse = $splits->filter(function (GetSplitResponse $item) {
            return $item->recipient->email == 'hotta.yoshiaki+teste-coproduction1@gmail.com';
        })->first();

        /** @var GetSplitResponse $clientSplitResponse */
        $clientSplitResponse = $splits->filter(function (GetSplitResponse $item) {
            return $item->recipient->email == 'cliente@xgrow.com';
        })->first();

        /** @var GetSplitResponse $xgrowSplitResponse */
        $xgrowSplitResponse = $splits->filter(function (GetSplitResponse $item) {
            return $item->recipient->email == 'feliped@fandone.com.br';
        })->first();

        $this->assertEquals($producerAmount, $coproducerSplitResponse->amount);
        $this->assertEquals($clientAmount, $clientSplitResponse->amount);
        $this->assertEquals($xgrowAmount, $xgrowSplitResponse->amount);
    }
}
