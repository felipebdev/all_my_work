<?php

namespace Tests\Feature\Api\Checkout\Information;

use Tests\TestCase;

/**
 * Test /api/checkout/installmentvalue route
 *
 * route(checkout.installmentvalue)
 */
class InstallmentValueTest extends TestCase
{

    public function test_checkout_installment_value_endpoint_2_installments()
    {
        $this->withoutMiddleware();

        // route(checkout.installmentvalue)
        $response = $this->post('/api/checkout/installmentvalue', [
            'total_value' => 100,
            'installment' => 2,
        ]);

        if ($response->getStatusCode() == 409) {
            $this->markTestSkipped('Gateway problem');
        }

        $response->assertStatus(200);
        $response->assertExactJson([
            [
                "installment" => 1,
                "value" => "100.00",
            ],
            [
                "installment" => 2,
                "value" => "52.20",
            ],
        ]);
    }

    public function test_checkout_installment_value_endpoint_12_installments()
    {
        $this->withoutMiddleware();

        $response = $this->post('/api/checkout/installmentvalue', [
            'total_value' => 100,
            'installment' => 12,
        ]);

        if ($response->getStatusCode() == 409) {
            $this->markTestSkipped('Gateway problem');
        }

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'installment',
                'value',
            ],
        ]);

        $response->assertExactJson([
            [
                "installment" => 1,
                "value" => "100.00",
            ],
            [
                "installment" => 2,
                "value" => "52.20",
            ],
            [
                "installment" => 3,
                "value" => "35.30",
            ],
            [
                "installment" => 4,
                "value" => "26.85",
            ],
            [
                "installment" => 5,
                "value" => "21.79",
            ],
            [
                "installment" => 6,
                "value" => "18.41",
            ],
            [
                "installment" => 7,
                "value" => "16.00",
            ],
            [
                "installment" => 8,
                "value" => "14.20",
            ],
            [
                "installment" => 9,
                "value" => "12.80",
            ],
            [
                "installment" => 10,
                "value" => "11.68",
            ],
            [
                "installment" => 11,
                "value" => "10.76",
            ],
            [
                "installment" => 12,
                "value" => "10.00",
            ]
        ]);
    }

}
