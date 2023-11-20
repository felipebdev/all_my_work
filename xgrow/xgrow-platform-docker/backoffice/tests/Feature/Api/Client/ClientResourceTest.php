<?php

namespace Tests\Feature\Api\Client;

use App\Client;
use App\Payment;
use App\Plan;
use App\Platform;
use App\Repositories\ClientRepository;
use App\Product;
use App\Subscriber;
use App\User;
use Illuminate\Http\UploadedFile;
use Tests\Feature\Traits\ClientTrait;
use Tests\Feature\Traits\PaymentTrait;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ClientResourceTest extends TestCase
{
    use ClientTrait;
    use PaymentTrait;
    protected string $endpoint = '/api/client';
    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new ClientRepository();
        $user = User::factory()->create();
        $this->token = JWTAuth::fromUser($user);
    }

    public function test_find_empty()
    {
        $response = $this->getJson("{$this->endpoint}?token={$this->token}");
        $response->assertStatus(200);
        $response->assertJsonCount(0, 'response.clients.data');
    }

    public function test_find_all()
    {
        Client::factory()->count(3)->create();
        $response = $this->getJson("{$this->endpoint}?token={$this->token}");
        $response->assertStatus(200);
        $response->assertJsonCount(3, 'response.clients.data');
    }

    public function test_paginate(){
        Client::factory()->count(5)->create();

        //First page
        $response = $this->getJson("{$this->endpoint}?token={$this->token}&offset=2");
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'response.clients.data');

        //Last page
        $response = $this->getJson("{$this->endpoint}?token={$this->token}&offset=2&page=3");
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'response.clients.data');

    }

    public function test_find_by_search()
    {
        $this->createClients();
        $response = $this->getJson("{$this->endpoint}?token={$this->token}&search=First");
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'response.clients.data');
        $this->assertEquals(
            'first_client@xgrow.com',
            $response['response']['clients']['data'][0]['email']
        );
    }

    public function test_find_by_created_period(){
        $this->createClients();

        $response = $this->getJson("{$this->endpoint}?token={$this->token}&period=08/05/2022 - 10/06/2022");
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'response.clients.data');
        $clients = $response['response']['clients']['data'];
        $this->assertNotContains('first_client@xgrow.com', array_merge_recursive(...$clients));
    }

    public function test_error_get_single_client()
    {
        $response = $this->getJson("{$this->endpoint}/1?token={$this->token}");
        $response->assertStatus(400);
    }

    public function test_get_single_client()
    {
        $this->createClients();
        $client = Client::first();
        $response = $this->getJson("{$this->endpoint}/$client->id?token={$this->token}");
        $this->assertEquals(
            $client->email,
            $response['response']['client']['email']
        );
    }

    public function test_store_client()
    {
        $data = [
            'first_name' => 'Fulano',
            'last_name' => 'De Tal',
            'email' => 'fulanodetal@xgrow.com.br',
            'password' => 'kX6783e1!',
            'password_confirmation' => 'kX6783e1!',
            'type_person' => 'F',
            'cpf' => '12345689000',
            'percent_split' => 1,
            'tax_transaction' => 1,
            'image' => UploadedFile::fake()->image('fake.png')
        ];

        $response = $this->postJson(
            "{$this->endpoint}?token={$this->token}",
            $data
        );
        $response->assertStatus(201);
        $this->assertDatabaseHas('clients', [
            'email' => $data['email'],
        ]);
    }

    public function test_update_client()
    {
        $client = Client::factory()->create();

        $data = [
            'first_name' => 'Fulano',
            'last_name' => 'De Tal',
            'email' => 'fulanodetal@xgrow.com.br',
            'password' => 'kX6783e1!',
            'password_confirmation' => 'kX6783e1!',
            'type_person' => 'F',
            'cpf' => '12345689000',
            'percent_split' => 1,
            'tax_transaction' => 1,
            'image' => UploadedFile::fake()->image('fake.png')
        ];

        $response = $this->putJson("$this->endpoint/{$client->id}?token={$this->token}", $data);
        $response->assertStatus(200);
        $client->refresh();
        $this->assertEquals($data['email'], $client->email);
    }

    public function test_delete_client()
    {
        $client = Client::factory()->create();

        $response = $this->deleteJson("$this->endpoint/{$client->id}?token={$this->token}");
        $response->assertStatus(200);
        $this->assertDatabaseMissing('clients', [
            'id' => $client->id
        ]);
    }
    /*
        public function test_summary_data_empty(){

            $response = $this->getJson("{$this->endpoint}/summary?token={$this->token}");

            $this->assertEquals(0, $response['response']['summary']['total_client']);
            $this->assertEquals(0, $response['response']['summary']['total_subscriber']);
            $this->assertEquals(0, $response['response']['summary']['total_seller']);
            $this->assertEquals(0, $response['response']['summary']['total_canceled']);
            $this->assertEquals(0, $response['response']['smary']['total_tax']);

        }

        public function test_summary_data(){

            $this->createSummaryData();

            $response = $this->getJson("{$this->endpoint}/summary?token={$this->token}");

            $this->assertEquals(1, $response['response']['summary']['total_client']);
            $this->assertEquals(1, $response['response']['summary']['total_subscriber']);
            $this->assertEquals(346.1, $response['response']['summary']['total_seller']);
            $this->assertEquals(135.70, $response['response']['summary']['total_canceled']);
            $this->assertEquals(20.50, $response['response']['summary']['total_tax']);

            //Filter by period
            $period = "25/04/2022 - 02/05/2022";
            $response = $this->getJson("{$this->endpoint}/summary?token={$this->token}&period={$period}");
            $this->assertEquals(1, $response['response']['summary']['total_client']);
            $this->assertEquals(0, $response['response']['summary']['total_subscriber']);
            $this->assertEquals(100.50, $response['response']['summary']['total_seller']);
            $this->assertEquals(135.70, $response['response']['summary']['total_canceled']);
            $this->assertEquals(0.00, $response['response']['summary']['total_tax']);

        }
    */
    public function test_client_summary_data_empty(){

        $client =  Client::factory()->create()->first();

        $response = $this->getJson("{$this->endpoint}/{$client->id}/summary?token={$this->token}");

        $this->assertEquals(0, $response['response']['summary']['total_subscriber']);
        $this->assertEquals(0, $response['response']['summary']['total_product']);
        $this->assertEquals(0, $response['response']['summary']['total_seller']);

    }


    public function test_client_summary_data(){

        $client =  Client::factory()->create(
            ['created_at' => '2022-05-01']
        )->first();

        $platform = Platform::factory()->create(
            ['customer_id' => $client->id]
        )->first();

        Product::factory()->count(2)->create(
            ['platform_id' => $platform->id]
        );
        Plan::factory()->count(2)->create();
        Subscriber::factory()->create();

        Payment::factory()->create([
            'customer_value' => 100.50,
            'payment_date' => '2022-05-01',
            'status' => Payment::STATUS_PAID
        ]);
        Payment::factory()->create([
            'customer_value' => 135.70,
            'payment_date' => '2022-05-02',
            'status' => Payment::STATUS_CANCELED,
            'tax_value' => 31.70
        ]);
        Payment::factory()->create([
            'customer_value' => 145.30,
            'payment_date' => '2022-05-05',
            'status' => Payment::STATUS_PAID
        ]);

        $response = $this->getJson("{$this->endpoint}/{$client->id}/summary?token={$this->token}");


        $this->assertEquals(1, $response['response']['summary']['total_subscriber']);
        $this->assertEquals(2, $response['response']['summary']['total_product']);
        $this->assertEquals(245.80, $response['response']['summary']['total_seller']);


        //Filter by period
        $period = "25/04/2022 - 02/05/2022";
        $response = $this->getJson("{$this->endpoint}/{$client->id}/summary?token={$this->token}&period={$period}");
        $this->assertEquals(0, $response['response']['summary']['total_subscriber']);
        $this->assertEquals(0, $response['response']['summary']['total_product']);
        $this->assertEquals(100.50, $response['response']['summary']['total_seller']);

    }


}
