<?php

namespace Tests\Feature\Api\Checkout\MultipleMeans;

use App\Payment;
use App\Subscriber;
use App\Subscription;
use Illuminate\Support\Facades\Queue;
use Illuminate\Testing\Fluent\AssertableJson;
use Modules\Integration\Queue\Jobs\HandleIntegration;
use Tests\Feature\Helper\MundipaggToken;
use Tests\Feature\Traits\CreateSubscriberTrait;
use Tests\Feature\Traits\Integration\MockPubSubTrait;
use Tests\Feature\Traits\Json\ValidatesCreditCardJsonPayload;
use Tests\Feature\Traits\LocalDatabaseIds;
use Tests\TestCase;

/**
 * Test Single Sale via Credit Card + Boleto
 */
class MultiMeansBCSaleTest extends TestCase
{

    use MockPubSubTrait;
    use CreateSubscriberTrait;
    use LocalDatabaseIds;
    use ValidatesCreditCardJsonPayload;

    public function test_multimeans_CCB()
    {
        Queue::fake();

        $this->withoutMiddleware();

        $this->mockPubSubWithCount(1);

        $subscriberId = $this->createSubscriber($this->platformId, $this->salePlanId);

        $token1 = MundipaggToken::cardOk($this->faker->creditCardNumber('MasterCard'));
        $token2 = MundipaggToken::cardOk($this->faker->creditCardNumber('Visa'));

        $response = $this->postJson("/api/checkout/{$this->platformId}/{$this->salePlanId}", [
            'payment_method' => 'multimeans',
            'subscriber_id' => $subscriberId,
            'platform_id' => $this->platformId,
            'plan_id' => $this->salePlanId, // 100
            //'order_bump' => $this->orderBumps,
            //'cupom' => '10REAIS',
            //'affiliate_id' => '2',
            'payments' => [
                [
                    'payment_method' => 'credit_card',
                    'token' => "$token1",
                    'installment' => 1,
                    'value' => '50.00',
                ],
                [
                    'payment_method' => 'credit_card',
                    'token' => "$token2",
                    'installment' => 1,
                    'value' => '30.00',
                ],
                [
                    'payment_method' => 'boleto',
                    'value' => '20.00',
                ]
            ],
        ]);

        //dump($response->json());

        if ($response->getStatusCode() == 409) {
            $this->markTestSkipped('Gateway problem');
        }

        $response->assertStatus(200);

        $response->assertJsonCount(3);

        $response->assertJsonStructure([
            '*' => [
                'status',
                'order_code',
                'boleto_pdf',
                'boleto_qrcode',
                'boleto_barcode',
                'boleto_url',
                'boleto_line',
                'pix_qrcode',
                'pix_qrcode_url',
                'magicToken',
                'one_click',
            ],
        ]);

        Queue::assertPushedOn('xgrow-jobs:integrations-checkout', HandleIntegration::class);

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
            'type_payment' => 'credit_card',
            'status' => Payment::STATUS_PENDING,
            'price' => 50,
        ]);

        $this->assertDatabaseHas('payments', [
            'order_code' => $orderCode,
            'type_payment' => 'credit_card',
            'status' => Payment::STATUS_PENDING,
            'price' => 30,
        ]);

        $this->assertDatabaseHas('payments', [
            'order_code' => $orderCode,
            'type_payment' => 'boleto',
            'status' => Payment::STATUS_PENDING,
            'price' => 20,
        ]);
    }

    public function test_CCB_without_coupon_without_orderBump_single_installment()
    {
        $this->withoutMiddleware();

        $this->mockPubSubWithCount(1);

        $subscriberId = $this->createSubscriber($this->platformId, $this->salePlanId);

        $token1 = MundipaggToken::randomValidCvv($this->faker->creditCardNumber('MasterCard'));
        $token2 = MundipaggToken::randomValidCvv($this->faker->creditCardNumber('Visa'));

        $response = $this->postJson("/api/checkout/{$this->platformId}/{$this->salePlanId}", [
            "payment_method" => "multimeans",
            "payments" => [
                [
                    "payment_method" => "credit_card",
                    "token" => "$token1",
                    "installment" => 1,
                    "value" => "50.00"
                ],
                [
                    "payment_method" => "credit_card",
                    "token" => "$token2",
                    "installment" => 1,
                    "value" => "30.00"
                ],
                [
                    "payment_method" => "boleto",
                    "value" => "20.00",
                ]
            ],
            "subscriber_id" => $subscriberId,
            "platform_id" => $this->platformId,
            "plan_id" => $this->salePlanId,
        ]);

        if ($response->getStatusCode() == 409) {
            $this->markTestSkipped('Gateway problem');
        }

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) {
            $json->has('0', function ($json) {
                // Credit Card 1
                $json->where('status', 'pending');
                $json->whereAllType([
                    'status' => 'string',
                    'order_code' => 'string',
                    'boleto_pdf' => 'null',
                    'boleto_qrcode' => 'null',
                    'boleto_barcode' => 'null',
                    'boleto_url' => 'null',
                    'boleto_line' => 'null',
                    'pix_qrcode' => 'null',
                    'pix_qrcode_url' => 'null',
                    'magicToken' => 'string',
                    'one_click' => 'string',
                ]);
            })->has('1', function ($json) {
                // Credit Card 2
                $json->where('status', 'pending');
                $json->whereAllType([
                    'status' => 'string',
                    'order_code' => 'string',
                    'boleto_pdf' => 'null',
                    'boleto_qrcode' => 'null',
                    'boleto_barcode' => 'null',
                    'boleto_url' => 'null',
                    'boleto_line' => 'null',
                    'pix_qrcode' => 'null',
                    'pix_qrcode_url' => 'null',
                    'magicToken' => 'string',
                    'one_click' => 'string',
                ]);
            })->has('2', function ($json) {
                // Boleto
                $json->where('status', 'pending');
                $json->whereAllType([
                    'status' => 'string',
                    'order_code' => 'string',
                    'boleto_pdf' => 'string',
                    'boleto_qrcode' => 'string',
                    'boleto_barcode' => 'string',
                    'boleto_url' => 'string',
                    'boleto_line' => 'string',
                    'pix_qrcode' => 'null',
                    'pix_qrcode_url' => 'null',
                    'magicToken' => 'string',
                    'one_click' => 'string',
                ]);
            });
        });
    }

    public function test_CCB_with_coupon_with_orderbump_and_installments_on_both()
    {
        $this->withoutMiddleware();

        $this->mockPubSubWithCount(1);

        $subscriberId = $this->createSubscriber($this->platformId, $this->salePlanId);

        $token1 = MundipaggToken::randomValidCvv($this->faker->creditCardNumber('MasterCard'));
        $token2 = MundipaggToken::randomValidCvv($this->faker->creditCardNumber('Visa'));

        $response = $this->postJson("/api/checkout/{$this->platformId}/{$this->salePlanId}", [
            "payment_method" => "multimeans",
            "cupom" => "10REAIS",
            "payments" => [
                [
                    "payment_method" => "credit_card",
                    "token" => "$token1",
                    "installment" => 2,
                    "value" => "70.00"
                ],
                [
                    "payment_method" => "credit_card",
                    "token" => "$token2",
                    "installment" => 2,
                    "value" => "40.00"
                ],
                [
                    "payment_method" => "boleto",
                    "value" => "30.00",
                ]
            ],
            "subscriber_id" => $subscriberId,
            "platform_id" => $this->platformId,
            "plan_id" => $this->salePlanId,
            "order_bump" => $this->orderBumps
        ]);

        if ($response->getStatusCode() == 409) {
            $this->markTestSkipped('Gateway problem');
        }

        $response->assertStatus(200);
    }


    public function test_four_cards_with_coupon_order_bump_and_interest()
    {
        $this->withoutMiddleware();

        $this->mockPubSubWithCount(1);

        $subscriberId = $this->createSubscriber($this->platformId, $this->salePlanId);

        $token1 = MundipaggToken::randomValidCvv($this->faker->creditCardNumber('MasterCard'));
        $token2 = MundipaggToken::randomValidCvv($this->faker->creditCardNumber('Visa'));
        $token3 = MundipaggToken::randomValidCvv($this->faker->creditCardNumber('American Express'));
        $token4 = MundipaggToken::randomValidCvv($this->faker->creditCardNumber('Visa'));

        $response = $this->postJson("/api/checkout/{$this->platformId}/{$this->salePlanId}", [
            "payment_method" => "multimeans",
            "cupom" => "10REAIS",
            "payments" => [
                [
                    "payment_method" => "credit_card",
                    "token" => "$token1",
                    "installment" => 1,
                    "value" => "40.00"
                ],
                [
                    "payment_method" => "credit_card",
                    "token" => "$token2",
                    "installment" => 1,
                    "value" => "30.0"
                ],
                [
                    "payment_method" => "credit_card",
                    "token" => "$token3",
                    "installment" => 1,
                    "value" => "20.0"
                ],
                [
                    "payment_method" => "boleto",
                    "token" => "$token4",
                    "installment" => 1,
                    "value" => "50.0"
                ]
            ],
            "subscriber_id" => $subscriberId,
            "platform_id" => $this->platformId,
            "plan_id" => $this->salePlanId,
            "order_bump" => $this->orderBumps
        ]);

        if ($response->getStatusCode() == 409) {
            $this->markTestSkipped('Gateway problem');
        }

        $response->assertStatus(200);
    }

    public function test_multiplemeans_fails_if_one_card_has_insuficient_balance()
    {
        $this->withoutMiddleware();

        $this->mockPubSubWithCount(1);

        $subscriberId = $this->createSubscriber($this->platformId, $this->salePlanId);

        $token1 = MundipaggToken::insufficientBalance($this->faker->creditCardNumber('MasterCard'));
        $token2 = MundipaggToken::randomValidCvv($this->faker->creditCardNumber('Visa'));

        $response = $this->postJson("/api/checkout/{$this->platformId}/{$this->salePlanId}", [
            "payment_method" => "multimeans",
            "payments" => [
                [
                    "payment_method" => "credit_card",
                    "token" => "$token1",
                    "installment" => 1,
                    "value" => "70.00"
                ],
                [
                    "payment_method" => "credit_card",
                    "token" => "$token2",
                    "installment" => 1,
                    "value" => "30.00"
                ]
            ],
            "subscriber_id" => $subscriberId,
            "platform_id" => $this->platformId,
            "plan_id" => $this->salePlanId,
        ]);

        $response->assertStatus(400);

        $response->assertJsonStructure([
            'message',
            'failures',
        ]);
    }
}
