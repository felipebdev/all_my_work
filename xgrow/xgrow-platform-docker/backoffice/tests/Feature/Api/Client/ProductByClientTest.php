<?php

namespace Tests\Feature\Api\Client;

use App\Client;
use App\Plan;
use App\PlanCategory;
use App\Platform;
use App\Product;
use App\User;
use Tests\Feature\Traits\ProductTrait;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProductByClientTest extends TestCase
{
    use ProductTrait;
    protected string $endpoint = '/api/client';
    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create();
        $this->token = JWTAuth::fromUser($user);
        PlanCategory::factory()->create();
    }

    public function test_find_empty()
    {
        $client = Client::factory()->create()->first();
        $response = $this->getJson("{$this->endpoint}/{$client->id}/product?token={$this->token}");
        $response->assertStatus(200);
        $response->assertJsonCount(0, 'response.products.data');
    }

    public function test_find_all()
    {
        $client = Client::factory()->create()->first();
        Platform::factory()->create();
        Product::factory()->count(2)->create();
        Plan::factory()->count(3)->create();
        $response = $this->getJson("{$this->endpoint}/{$client->id}/product?token={$this->token}");
        $response->assertStatus(200);
        $response->assertJsonCount(3, 'response.products.data');
    }

    public function test_error_get_single_client()
    {
        $response = $this->getJson("{$this->endpoint}/0/product?token={$this->token}");
        $response->assertStatus(400);
    }

    public function test_paginate(){
        $client = Client::factory()->create()->first();
        Platform::factory()->create();
        Product::factory()->count(2)->create();
        Plan::factory()->count(5)->create();

        //First page
        $response = $this->getJson("{$this->endpoint}/{$client->id}/product?token={$this->token}&offset=2");
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'response.products.data');

        //Last page
        $response = $this->getJson("{$this->endpoint}/{$client->id}/product?token={$this->token}&offset=2&page=3");
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'response.products.data');
    }

    public function test_find_by_search()
    {
        $client = Client::factory()->create()->first();
        $this->createProducts($client->id);
        $response = $this->getJson("{$this->endpoint}/{$client->id}/product?token={$this->token}&search=Second");
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'response.products.data');
        $this->assertEquals(
            'Item 2',
            $response['response']['products']['data'][0]['description']
        );
    }

    public function test_product_data_list(){
        $client = Client::factory()->create()->first();
        Platform::factory()->create();
        $product = Product::factory()->count(1)->create()->first();
        Plan::factory()->count(1)->create();
        $response = $this->getJson("{$this->endpoint}/{$client->id}/product?token={$this->token}");
        $response->assertJsonCount(1, 'response.products.data');
        $this->assertEquals($product->name, $response['response']['products']['data'][0]['product_name']);
    }

}
