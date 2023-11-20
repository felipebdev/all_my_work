<?php

namespace Tests\Feature\Api\Refund;

use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\Fluent\AssertableJson;
use Modules\Integration\Enums\EventEnum;
use Modules\Integration\Queue\Jobs\HandleIntegration;
use Tests\Feature\Helper\JwtWebToken;
use Tests\Feature\Helper\MundipaggToken;
use Tests\Feature\Traits\CreateSubscriberTrait;
use Tests\Feature\Traits\Integration\MockPubSubTrait;
use Tests\Feature\Traits\LocalDatabaseIds;
use Tests\TestCase;

class RefundCreateCreditCardTest extends TestCase
{

    use MockPubSubTrait;
    use LocalDatabaseIds;
    use CreateSubscriberTrait;

    public function test_create_credit_card_refund()
    {
        $this->withoutMiddleware();

        $this->mockPubSubWithCount(1);

        $subscriberId = $this->createSubscriber($this->platformId, $this->salePlanId);

        $token = MundipaggToken::cardOk($this->faker->creditCardNumber('MasterCard'));

        $response = $this->postJson("/api/checkout/{$this->platformId}/{$this->salePlanId}", [
            "payment_method" => "credit_card",
            "cc_info" => [
                [
                    "token" => "$token",
                    "installment" => 4,
                    "value" => "100.00"
                ]
            ],
            "subscriber_id" => $subscriberId,
            "platform_id" => $this->platformId,
            "plan_id" => $this->salePlanId,
            "order_bump" => []
        ]);

        if ($response->getStatusCode() == 409) {
            $this->markTestSkipped('Gateway problem');
        }

        $response->assertStatus(200);

        $this->withMiddleware();

        $jwt = JwtWebToken::generateToken($this->platformId, $this->platformUserId);

        $paymentId = DB::table('payments')->whereNotNull('order_id')->latest('id')->first()->id;

        Bus::fake();

        $response2 = $this->withHeader('Authorization', 'Bearer '.$jwt)
            ->postJson('/api/refund', [
                'payment_method' => 'credit_card',
                'payment_id' => "$paymentId",
                'reason' => 'Refund test',
                'metadata' => ['description' => 'refund test'],
                //'refund_all' => true
            ]);

        Bus::assertDispatched(HandleIntegration::class, function (HandleIntegration $integration) {
            return $integration->event === EventEnum::PAYMENT_REFUND;
        });

        $response2->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json->first(fn($json) => $json->whereAllType([
                    'object' => 'string',
                    'id' => 'integer',
                    'amount' => 'integer',
                    'type' => 'string',
                    'status' => 'string',
                    'is_partial' => 'boolean',
                    'transaction_id' => 'string|null',
                    'created_at' => 'string',
                    'metadata' => 'array',
                    'is_partial' => 'boolean',
                ])
                // ->etc()
                );
            });
    }
}
