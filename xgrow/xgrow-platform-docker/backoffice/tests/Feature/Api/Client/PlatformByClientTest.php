<?php

namespace Tests\Feature\Api\Client;

use App\Client;
use App\Platform;
use App\User;
use Tests\Feature\Traits\PlatformTrait;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class PlatformByClientTest extends TestCase
{
    use PlatformTrait;
    protected string $endpoint = '/api/client';
    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create();
        $this->token = JWTAuth::fromUser($user);
    }

    public function test_find_empty()
    {
        $client = Client::factory()->create()->first();
        $response = $this->getJson("{$this->endpoint}/{$client->id}/platform?token={$this->token}");
        $response->assertStatus(200);
        $response->assertJsonCount(0, 'response.platforms.data');
    }

    public function test_find_all()
    {
        $client = Client::factory()->create()->first();
        Platform::factory()->count(3)->create();
        $response = $this->getJson("{$this->endpoint}/{$client->id}/platform?token={$this->token}");
        $response->assertStatus(200);
        $response->assertJsonCount(3, 'response.platforms.data');
    }

    public function test_error_get_single_client()
    {
        $response = $this->getJson("{$this->endpoint}/0/platform?token={$this->token}");
        $response->assertStatus(400);
    }

    public function test_paginate(){
        $client = Client::factory()->create()->first();
        Platform::factory()->count(5)->create();

        //First page
        $response = $this->getJson("{$this->endpoint}/{$client->id}/platform?token={$this->token}&offset=2");
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'response.platforms.data');

        //Last page
        $response = $this->getJson("{$this->endpoint}/{$client->id}/platform?token={$this->token}&offset=2&page=3");
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'response.platforms.data');
    }


    public function test_find_by_search()
    {
        $client = Client::factory()->create()->first();
        $this->createPlatforms($client->id);
        $response = $this->getJson("{$this->endpoint}/{$client->id}/platform?token={$this->token}&search=Second");
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'response.platforms.data');
        $this->assertEquals(
            'fghij',
            $response['response']['platforms']['data'][0]['id']
        );
    }

    public function test_platform_data_list(){
        $client = Client::factory()->create()->first();
        $platform = Platform::factory()->create()->first();
        $response = $this->getJson("{$this->endpoint}/{$client->id}/platform?token={$this->token}");
        $response->assertJsonCount(1, 'response.platforms.data');
        $this->assertEquals($platform->name, $response['response']['platforms']['data'][0]['name']);
        $this->assertEquals($platform->url, $response['response']['platforms']['data'][0]['url']);
        $this->assertEquals($client->company_name, $response['response']['platforms']['data'][0]['company_name']);
    }

}
