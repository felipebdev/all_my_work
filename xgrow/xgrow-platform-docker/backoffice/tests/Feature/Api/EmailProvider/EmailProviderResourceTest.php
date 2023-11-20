<?php

namespace Tests\Feature\Api\EmailProvider;

use App\EmailProvider;
use App\Repositories\EmailProviderRepository;
use Tests\Feature\Traits\EmailProviderTrait;
use App\User;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class EmailProviderResourceTest extends TestCase
{
    use EmailProviderTrait;
    protected string $endpoint = '/api/email-provider';
    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EmailProviderRepository();
        $user = User::factory()->create();
        $this->token = JWTAuth::fromUser($user);
    }

    public function test_find_empty()
    {
        $response = $this->getJson("{$this->endpoint}?token={$this->token}");
        $response->assertStatus(200);
        $response->assertJsonCount(0, 'response.providers.data');
    }

    public function test_find_all()
    {
        EmailProvider::factory()->count(3)->create();
        $response = $this->getJson("{$this->endpoint}?token={$this->token}");
        $response->assertStatus(200);
        $response->assertJsonCount(3, 'response.providers.data');
    }

    public function test_paginate(){

        EmailProvider::factory()->count(5)->create();

        //First page
        $response = $this->getJson("{$this->endpoint}?token={$this->token}&offset=2");
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'response.providers.data');

        //Last page
        $response = $this->getJson("{$this->endpoint}?token={$this->token}&offset=2&page=3");
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'response.providers.data');

    }

    public function test_error_get_single_client()
    {
        $response = $this->getJson("{$this->endpoint}/1?token={$this->token}");
        $response->assertStatus(400);
    }

    public function test_get_single_client()
    {
        $emailProvider = EmailProvider::factory()->create();
        $response = $this->getJson("{$this->endpoint}/{$emailProvider->id}?token={$this->token}");
        $this->assertEquals(
            $emailProvider->name,
            $response['response']['provider']['name']
        );
    }

    public function test_apply_email_provider_cache(){
        $emailProvider = EmailProvider::factory(
            ['name' => 'test']
        )->create();
        $response = $this->postJson("{$this->endpoint}/apply?token={$this->token}&provider={$emailProvider->name}");
        $response->assertStatus(200)
            ->assertJsonStructure([
            'error', 'message', 'response'
        ]);
    }

    public function test_get_email_provider_drivers(){
        $response = $this->getJson("{$this->endpoint}/get-drivers?token={$this->token}");
        $response->assertStatus(200)
            ->assertJsonStructure([
                'error', 'message', 'response' => [
                    'drivers'
                ]
            ]);
    }

    public function test_get_data_providers(){
        $response = $this->getJson("{$this->endpoint}/get-data-provider?token={$this->token}");
        $response->assertStatus(200)
            ->assertJsonStructure([
                'error', 'message', 'response' => [
                    'defaultProvider' , 'cachedProvider'
                ]
            ]);
    }

    public function test_store_email_provider()
    {
        $data = [
            'settings' => json_encode([
                'key' => '123456',
                'secret' => 'your-ses-secret',
                'region' => 'ses-region (e.g. us-east-1)'
            ]),
            'name' => 'teste',
            'from_name' => 'Teste Email Provider',
            'from_address' => 'address@xgrow.com',
            'driver' => array_random(EmailProvider::DRIVERS),
            'service_tags' => 'provider, email',
            'description' => 'Email Provider Description'
        ];

        $response = $this->postJson(
            "{$this->endpoint}?token={$this->token}",
            $data
        );
        $response->assertStatus(201);
        $this->assertDatabaseHas('email_providers', [
            'from_name' => $data['from_name'],
        ]);
    }

    public function test_update_email_provider()
    {
        $emailProvider = EmailProvider::factory()->create();

        $data = [
            'settings' => json_encode([
                'key' => '123456',
                'secret' => 'your-ses-secret',
                'region' => 'ses-region (e.g. us-east-1)'
            ]),
            'name' => 'teste',
            'from_name' => 'Teste Email Provider',
            'from_address' => 'address@xgrow.com',
            'driver' => array_random(EmailProvider::DRIVERS),
            'service_tags' => 'provider, email',
            'description' => 'Email Provider Description'
        ];

        $this->assertNotEquals($data['from_address'], $emailProvider->from_address);

        $response = $this->putJson("$this->endpoint/{$emailProvider->id}?token={$this->token}", $data);
        $response->assertStatus(200);
        $emailProvider->refresh();
        $this->assertEquals($data['from_address'], $emailProvider->from_address);
    }

    public function test_delete_email()
    {
        $emailProvider = EmailProvider::factory()->create();

        $response = $this->deleteJson("$this->endpoint/{$emailProvider->id}?token={$this->token}");
        $response->assertStatus(200);
        $this->assertDatabaseMissing('email_providers', [
            'id' => $emailProvider->id
        ]);
    }

}

?>
