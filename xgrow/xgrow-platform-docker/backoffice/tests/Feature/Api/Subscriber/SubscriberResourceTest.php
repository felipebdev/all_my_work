<?php

namespace Tests\Feature\Api\Subscriber;

use App\User;
use App\Client;
use App\Platform;
use App\Subscriber;
use App\Repositories\SubscriberRepository;
use Illuminate\Http\UploadedFile;
use Tests\Feature\Traits\SubscriberTrait;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class SubscriberResourceTest extends TestCase
{
    use SubscriberTrait;
    protected string $endpoint = '/api/subscriber';
    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new SubscriberRepository();
        $user = User::factory()->create();
        $this->token = JWTAuth::fromUser($user);
    }

    public function test_find_empty()
    {
        $response = $this->getJson("{$this->endpoint}?token={$this->token}");
        $response->assertStatus(200);
        $response->assertJsonCount(0, 'response.subscribers.data');
    }

    public function test_find_all()
    {
        Client::factory()->create();
        Platform::factory()->create();
        Subscriber::factory()->count(3)->create();
        $response = $this->getJson("{$this->endpoint}?token={$this->token}");
        $response->assertStatus(200);
        $response->assertJsonCount(3, 'response.subscribers.data');
    }

    public function test_paginate()
    {
        Client::factory()->create();
        Platform::factory()->create();
        Subscriber::factory()->count(5)->create();

        //First page
        $response = $this->getJson("{$this->endpoint}?token={$this->token}&offset=2");
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'response.subscribers.data');

        //Last page
        $response = $this->getJson("{$this->endpoint}?token={$this->token}&offset=2&page=3");
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'response.subscribers.data');

    }

    public function test_find_by_search()
    {
        $this->createSubscribers();
        $response = $this->getJson("{$this->endpoint}?token={$this->token}&search=Joao");
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'response.subscribers.data');
        $this->assertEquals(
            'joao@xgrow.com',
            $response['response']['subscribers']['data'][0]['email']
        );
    }

    public function test_error_get_single_subscriber()
    {
        $response = $this->getJson("{$this->endpoint}/1?token={$this->token}");
        $response->assertStatus(400);
    }

    public function test_get_single_subscriber()
    {
        $this->createSubscribers();
        $subscriber = Subscriber::first();
        $response = $this->getJson("{$this->endpoint}/$subscriber->id?token={$this->token}");
        $this->assertEquals(
            $subscriber->email,
            $response['response']['subscriber']['email']
        );
    }

}
