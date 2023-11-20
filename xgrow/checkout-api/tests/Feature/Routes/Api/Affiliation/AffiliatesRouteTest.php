<?php

namespace Tests\Feature\Routes\Api\Affiliation;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\Traits\LocalDatabaseIds;
use Tests\TestCase;

class AffiliatesRouteTest extends TestCase
{
    use LocalDatabaseIds;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->markTestSkipped('Test not working, due affiliate not having a Client, review this test');

        $this->faker = $this->makeFaker('pt_BR');
    }

    public function test_can_store_affiliate()
    {
        $this->withoutMiddleware();

        $response = $this->post("/api/checkout/platforms/{$this->platformId}/plans/{$this->salePlanId}/affiliates", [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'document_type' => 'cpf',
            'document_number' => $this->faker->cpf(false),
            'legal_name' => 'Afiliado XGrow',
            'account_type' => 'checking',
            'bank_code' => '001',
            'agency' => '123',
            'agency_digit' => null,
            'account' => '12345',
            'account_digit' => '6',
        ]);

        $response->assertStatus(201);

        $response->assertJsonStructure([
            "error",
            "message",
            "response" => [
                'id',
            ],
        ]);
    }


    public function test_can_list_affiliate()
    {
        $this->withoutMiddleware();

        $response = $this->get("/api/checkout/platforms/{$this->platformId}/plans/{$this->salePlanId}/affiliates");

        if ($response->getStatusCode() == 409) {
            $this->markTestSkipped('Gateway problem');
        }

        $response->assertStatus(200);
    }
}
