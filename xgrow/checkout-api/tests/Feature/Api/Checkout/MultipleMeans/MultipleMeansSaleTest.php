<?php

namespace Tests\Feature\Api\Checkout\MultipleMeans;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\Helper\MundipaggToken;
use Tests\Feature\Traits\CreateSubscriberTrait;
use Tests\Feature\Traits\Integration\MockPubSubTrait;
use Tests\Feature\Traits\Json\ValidatesCreditCardJsonPayload;
use Tests\Feature\Traits\LocalDatabaseIds;
use Tests\TestCase;

/**
 * Test Multiple Credit Cards Sale Test
 */
class MultipleMeansSaleTest extends TestCase
{

    use MockPubSubTrait;
    use WithFaker;
    use CreateSubscriberTrait;
    use LocalDatabaseIds;
    use ValidatesCreditCardJsonPayload;

    public function test_two_cards_without_coupon_without_orderBump_single_installment()
    {
        $this->withoutMiddleware();

        $this->mockPubSubWithCount(1);

        $subscriberId = $this->createSubscriber($this->platformId, $this->salePlanId);

        $token1 = MundipaggToken::randomValidCvv($this->faker->creditCardNumber('MasterCard'));
        $token2 = MundipaggToken::randomValidCvv($this->faker->creditCardNumber('Visa'));

        $response = $this->postJson("/api/checkout/{$this->platformId}/{$this->salePlanId}", [
            "payment_method" => "credit_card",
            "cc_info" => [
                [
                    "token" => "$token1",
                    "installment" => 1,
                    "value" => "70.00"
                ],
                [
                    "token" => "$token2",
                    "installment" => 1,
                    "value" => "30.00"
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
        $this->assertJsonPayload($response);
    }

    public function test_two_cards_with_coupon_with_orderbump_and_installments_on_both()
    {
        $this->withoutMiddleware();

        $this->mockPubSubWithCount(1);

        $subscriberId = $this->createSubscriber($this->platformId, $this->salePlanId);

        $token1 = MundipaggToken::randomValidCvv($this->faker->creditCardNumber('MasterCard'));
        $token2 = MundipaggToken::randomValidCvv($this->faker->creditCardNumber('Visa'));

        $response = $this->postJson("/api/checkout/{$this->platformId}/{$this->salePlanId}", [
            "payment_method" => "credit_card",
            "cupom" => "10REAIS",
            "cc_info" => [
                [
                    "token" => "$token1",
                    "installment" => 3,
                    "value" => "90.00"
                ],
                [
                    "token" => "$token2",
                    "installment" => 2,
                    "value" => "50.00"
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

        $this->assertJsonPayload($response);
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
            "payment_method" => "credit_card",
            "cupom" => "10REAIS",
            "cc_info" => [
                [
                    "token" => "$token1",
                    "installment" => 1,
                    "value" => "40.00"
                ],
                [
                    "token" => "$token2",
                    "installment" => 1,
                    "value" => "30.0"
                ],
                [
                    "token" => "$token3",
                    "installment" => 1,
                    "value" => "20.0"
                ],
                [
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

        $this->assertJsonPayload($response);
    }

    public function test_multiplemeans_fails_if_one_card_has_insuficient_balance()
    {
        $this->withoutMiddleware();

        $this->mockPubSubWithCount(1);

        $subscriberId = $this->createSubscriber($this->platformId, $this->salePlanId);

        $token1 = MundipaggToken::insufficientBalance($this->faker->creditCardNumber('MasterCard'));
        $token2 = MundipaggToken::randomValidCvv($this->faker->creditCardNumber('Visa'));

        $response = $this->postJson("/api/checkout/{$this->platformId}/{$this->salePlanId}", [
            "payment_method" => "credit_card",
            "cc_info" => [
                [
                    "token" => "$token1",
                    "installment" => 1,
                    "value" => "70.00"
                ],
                [
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
