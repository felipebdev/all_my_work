<?php

namespace Tests\Feature\Api\Platform;

use App\Permission;
use App\Client;
use App\Platform;
use App\Product;
use App\Repositories\PlatformRepository;
use App\User;
use Illuminate\Http\UploadedFile;
use Tests\Feature\Traits\PaymentTrait;
use Tests\Feature\Traits\PlatformTrait;
use Tests\Feature\Traits\ProductTrait;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class PlatformResourceTest extends TestCase
{
    use PlatformTrait;
    use PaymentTrait;
    use ProductTrait;
    protected string $endpoint = '/api/platform';
    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new PlatformRepository();
        $user = User::factory()->create();
        $this->token = JWTAuth::fromUser($user);
    }

    public function test_find_empty()
    {
        $response = $this->getJson("{$this->endpoint}?token={$this->token}");
        $response->assertStatus(200);
        $response->assertJsonCount(0, 'response.platforms.data');
    }

    public function test_find_all()
    {
        Client::factory()->create();
        Platform::factory()->count(3)->create();
        $response = $this->getJson("{$this->endpoint}?token={$this->token}");
        $response->assertStatus(200);
        $response->assertJsonCount(3, 'response.platforms.data');
    }


    public function test_paginate(){
        Client::factory()->create();
        Platform::factory()->count(5)->create();

        //First page
        $response = $this->getJson("{$this->endpoint}?token={$this->token}&offset=2");
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'response.platforms.data');

        //Last page
        $response = $this->getJson("{$this->endpoint}?token={$this->token}&offset=2&page=3");
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'response.platforms.data');

    }

    public function test_find_by_search()
    {
        $client = Client::factory()->create();
        $this->createPlatforms($client->id);
        $response = $this->getJson("{$this->endpoint}?token={$this->token}&search=First");
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'response.platforms.data');
        $this->assertEquals(
            'https://www.first.com',
            $response['response']['platforms']['data'][0]['url']
        );
    }

    public function test_error_get_single_platform()
    {
        $response = $this->getJson("{$this->endpoint}/1?token={$this->token}");
        $response->assertStatus(400);
    }

    public function test_get_single_platform()
    {
        $client = Client::factory()->create();
        $this->createPlatforms($client->id);
        $platform = Platform::first();
        $response = $this->getJson("{$this->endpoint}/$platform->id?token={$this->token}");
        $this->assertEquals(
            $platform->id,
            $response['response']['platform']['id']
        );
    }

    public function test_store_platform()
    {
        $client = Client::factory()->create();
        $data = [
            'name' => 'México',
            'url' => 'http://www.mexico.com',
            'slug' => 'mexico slug',
            'customer_id' => $client->id,
            'restrict_ips' => '1',
            'ips_available' => 'http://127.0.0.1',
            'name_slug' => 'teste mexico',
            'cover' => UploadedFile::fake()->image('fake.png')
        ];

        $response = $this->postJson(
            "{$this->endpoint}?token={$this->token}",
            $data
        );
        $response->assertStatus(201);
        $this->assertDatabaseHas('platforms', [
            'name' => $data['name'],
        ]);
    }

    public function test_update_platform()
    {
        $client = Client::factory()->create();
        Platform::factory()->create();

        $platform = Platform::first();
        $changedName = $platform->name . " changed";

        $data = [
            'name' => $changedName,
            'url' => 'http://www.mexico.com',
            'restrict_ips' => '0',
            'customer_id' => $client->id,
            'name_slug' => 'teste mexico',
            'cover' => UploadedFile::fake()->image('fake.png')
        ];

        $response = $this->putJson("$this->endpoint/{$platform->id}?token={$this->token}", $data);
        $response->assertStatus(200);
        $platform->refresh();
        $this->assertEquals($data['name'], $platform->name);
    }

    public function test_delete_platform()
    {
        Client::factory()->create();
        Platform::factory()->create();

        $platform = Platform::first();

        $response = $this->deleteJson("$this->endpoint/{$platform->id}?token={$this->token}");
        $response->assertStatus(204);
        $this->assertNull(Platform::first());

    }

    public function test_summary_data_empty(){

        $response = $this->getJson("{$this->endpoint}/summary?token={$this->token}");

        $this->assertEquals(0, $response['response']['summary']['total_platform']);
        $this->assertEquals(0, $response['response']['summary']['total_subscriber']);
        $this->assertEquals(0, $response['response']['summary']['total_seller']);
        $this->assertEquals(0, $response['response']['summary']['total_canceled']);
        $this->assertEquals(0, $response['response']['summary']['total_tax']);

    }

    public function test_summary_data(){

        $this->createSummaryData();

        $response = $this->getJson("{$this->endpoint}/summary?token={$this->token}");

        $this->assertEquals(1, $response['response']['summary']['total_platform']);
        $this->assertEquals(1, $response['response']['summary']['total_subscriber']);
        $this->assertEquals(346.1, $response['response']['summary']['total_seller']);
        $this->assertEquals(135.70, $response['response']['summary']['total_canceled']);
        $this->assertEquals(20.50, $response['response']['summary']['total_tax']);

        //Filter by period
        $period = "25/04/2022 - 02/05/2022";
        $response = $this->getJson("{$this->endpoint}/summary?token={$this->token}&period={$period}");
        $this->assertEquals(0, $response['response']['summary']['total_platform']);
        $this->assertEquals(0, $response['response']['summary']['total_subscriber']);
        $this->assertEquals(100.50, $response['response']['summary']['total_seller']);
        $this->assertEquals(135.70, $response['response']['summary']['total_canceled']);
        $this->assertEquals(0.00, $response['response']['summary']['total_tax']);

    }

    public function test_get_platform_by_name_empty(){
        $response = $this->getJson("{$this->endpoint}/get-by-name?token={$this->token}");
        $response->assertJsonCount(0, 'response.platforms');
    }

    public function test_get_platform_by_name(){
        $client = Client::factory()->create();
        $this->createPlatforms($client->id);

        //test without search
        $response = $this->getJson("{$this->endpoint}/get-by-name?token={$this->token}");
        $response->assertJsonCount(3, 'response.platforms');

        $response = $this->getJson("{$this->endpoint}/get-by-name?token={$this->token}&search=");
        $response->assertJsonCount(3, 'response.platforms');

        //test with search
        $response = $this->getJson("{$this->endpoint}/get-by-name?token={$this->token}&search=Second");
        $response->assertJsonCount(1, 'response.platforms');
        $this->assertEquals('fghij', $response['response']['platforms'][0]['id']);
    }

    public function test_get_product_by_platform_empty(){
        Client::factory()->create();
        Platform::factory()->create();
        $platform = Platform::first();
        $response = $this->getJson("{$this->endpoint}/{$platform->id}/product?token={$this->token}");
        $response->assertJsonCount(0, 'response.products');
    }

    public function test_get_product_by_platform(){
        $client = Client::factory([
            'first_name' => 'Customer',
            'last_name' => 'Surname',
        ])->create();
        $this->createProducts($client->id);
        $product = Product::first();
        $platform = Platform::first();
        $response = $this->getJson("{$this->endpoint}/{$platform->id}/product?token={$this->token}");

        $response->assertJsonCount(3, 'response.products');
        $this->assertEquals($product->id, $response['response']['products'][0]['product_id']);
        $this->assertEquals('First', $response['response']['products'][0]['product_name']);
        $this->assertEquals('First Category', $response['response']['products'][0]['category_name']);
        $this->assertEquals('Customer Surname', $response['response']['products'][0]['customer_name']);
        $this->assertEquals('My Platform', $response['response']['products'][0]['platform_name']);
        $this->assertEquals(110.50, $response['response']['products'][0]['price']);
        $this->assertEquals('under_analysis', $response['response']['products'][0]['analysis_status']);
    }

    public function test_error_get_platform_permission()
    {
        $response = $this->getJson("{$this->endpoint}/1/permission?token={$this->token}");
        $response->assertStatus(400);
    }

    public function test_get_platform_permission_empty(){
        Client::factory()->create();
        Platform::factory()->create();
        $platform = Platform::first();
        $response = $this->getJson("{$this->endpoint}/{$platform->id}/permission?token={$this->token}");
        $response->assertJsonCount(0, 'response.permissions');
    }

    public function test_get_platform_permission(){
        Client::factory()->create();
        Platform::factory()->create();
        $platform = Platform::first();
        Permission::factory(
            ['platform_id' => $platform->id]
        )->count(5)->create();
        $response = $this->getJson("{$this->endpoint}/{$platform->id}/permission?token={$this->token}");

        $response->assertJsonCount(5, 'response.permissions');
        $permission = Permission::find(2);

        $this->assertEquals($permission->id, $response['response']['permissions'][1]['id']);
        $this->assertEquals($permission->name, $response['response']['permissions'][1]['name']);
    }

}
