<?php

namespace Tests\Feature\Routes\Api\Affiliation;

use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Feature\Traits\LocalDatabaseIds;
use Tests\TestCase;

class AffiliationSettingsTests extends TestCase
{
    use LocalDatabaseIds;

    public function test_affiliation_not_enabled()
    {
        $this->withoutMiddleware();

        $response = $this->get("/api/checkout/platforms/{$this->platformId}/plans/{$this->subscriptionPlanId}/affiliation/settings");

        $response->assertStatus(404);
        //$response->assertStatus(503); // Configuração de afiliação incorreta

        $response->assertJson(fn(AssertableJson $json) => $json->whereAllType([
            'error' => 'boolean',
            'message' => 'string',
            'response' => 'array',
        ]));

        $this->assertEquals('Afiliação desabilitada para o plano', $response->json('message'));
    }

    public function test_affiliation_settings_payload()
    {
        $this->withoutMiddleware();

        $response = $this->get("/api/checkout/platforms/{$this->platformId}/plans/{$this->salePlanId}/affiliation/settings");

        //echo(json_encode($response->json(), JSON_PRETTY_PRINT));

        if ($response->getStatusCode() == 409) {
            $this->markTestSkipped('Gateway problem');
        }

        $response->assertStatus(200);

        $response->assertJson(fn(AssertableJson $json) => $json->whereAllType([
            'error' => 'boolean',
            'message' => 'string',
            'response' => 'array',
            'response.id' => 'integer',
            'response.product_id' => 'integer',
            'response.approve_request_manually' => 'boolean|integer',
            'response.receive_email_notifications' => 'boolean|integer',
            'response.buyers_data_access_allowed' => 'boolean|integer',
            'response.support_email' => 'string|null',
            'response.instructions' => 'string|null',
            'response.commission' => 'string|null',
            'response.cookie_duration' => "string",
            'response.assignment' => "string", // "last_click",
            'response.invite_link' => 'string|null',
            'response.created_at' => 'string|null',
            'response.updated_at' => 'string|null',
        ]));
    }

    public function test_update_affiliation_settings()
    {
        $this->withoutMiddleware();

        $response = $this->put("/api/checkout/platforms/{$this->platformId}/plans/{$this->salePlanId}/affiliation/settings",
            [
                'enabled' => true, // required
                "approve_request_manually" => true, // optional
                "receive_email_notifications" => true, // optional
                "buyers_data_access_allowed" => true, // optional
                "support_email" => 'b@1.com', // optional
                "instructions" => 'instruction', // optional
                "commission" => "10.5", // optional
                "cookie_duration" => "30", // optional
                "assignment" => "first_click", // optional
                "invite_link" => 'etc.google.com', // optional
            ]);

        //echo(json_encode($response->json(), JSON_PRETTY_PRINT));

        if ($response->getStatusCode() == 409) {
            $this->markTestSkipped('Gateway problem');
        }

        $response->assertStatus(200);

        $response->assertJson(fn(AssertableJson $json) => $json->whereAllType([
            'error' => 'boolean',
            'message' => 'string',
            'response' => 'array',
            'response.id' => 'integer',
            'response.product_id' => 'integer',
            'response.approve_request_manually' => 'boolean|integer',
            'response.receive_email_notifications' => 'boolean|integer',
            'response.buyers_data_access_allowed' => 'boolean|integer',
            'response.support_email' => 'string|null',
            'response.instructions' => 'string|null',
            'response.commission' => 'string|null',
            'response.cookie_duration' => "string",
            'response.assignment' => "string", // "last_click",
            'response.invite_link' => 'string|null',
            'response.created_at' => 'string|null',
            'response.updated_at' => 'string|null',
        ]));
    }

}
