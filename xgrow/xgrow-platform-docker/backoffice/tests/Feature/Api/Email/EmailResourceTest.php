<?php

namespace Tests\Feature\Api\Email;

use App\User;
use App\Email;
use App\Repositories\EmailRepository;
use Tests\Feature\Traits\EmailTrait;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class EmailResourceTest extends TestCase
{
    use EmailTrait;
    protected string $endpoint = '/api/email';
    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EmailRepository();
        $user = User::factory()->create();
        $this->token = JWTAuth::fromUser($user);
    }

    public function test_find_ten_defaults_values()
    {
        $response = $this->getJson("{$this->endpoint}?token={$this->token}");
        $response->assertStatus(200);
        $response->assertJsonCount(10, 'response.emails.data');
    }

    public function test_find_all_included_created_values()
    {
        Email::factory()->count(3)->create();
        $response = $this->getJson("{$this->endpoint}?token={$this->token}");
        $response->assertStatus(200);
        $response->assertJsonCount(13, 'response.emails.data');
    }

    public function test_paginate(){
        Email::factory()->count(5)->create();

        //First page
        $response = $this->getJson("{$this->endpoint}?token={$this->token}&offset=2");
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'response.emails.data');

        //Last page
        $response = $this->getJson("{$this->endpoint}?token={$this->token}&offset=2&page=8");
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'response.emails.data');

    }

    public function test_find_by_search()
    {
        $this->createEmails();
        $response = $this->getJson("{$this->endpoint}?token={$this->token}&search=Opção DEF");
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'response.emails.data');
        $this->assertEquals(
            'messageDEF@xgrow.com',
            $response['response']['emails']['data'][0]['from']
        );
    }

    public function test_error_get_single_email()
    {
        $response = $this->getJson("{$this->endpoint}/1?token={$this->token}");
        $response->assertStatus(400);
    }

    public function test_get_single_email()
    {
        $this->createEmails();
        $email = Email::first();
        $response = $this->getJson("{$this->endpoint}/$email->id?token={$this->token}");
        $this->assertEquals(
            $email->from,
            $response['response']['email']['from']
        );
    }

    public function test_store_email()
    {
        $data = [
            'area' => '2',
            'subject' => 'Ação ABC',
            'message' => 'E-mail de resposta para a ação ABC.',
            'from' => 'acaoABC@xgrow.com',
        ];

        $response = $this->postJson(
            "{$this->endpoint}?token={$this->token}",
            $data
        );
        $response->assertStatus(201);
        $this->assertDatabaseHas('emails', [
            'from' => $data['from'],
        ]);
    }

    public function test_update_email()
    {
        $email = Email::factory()->create();

        $data = [
            'area' => '2',
            'subject' => 'Ação ABC',
            'message' => 'E-mail de resposta para a ação ABC.',
            'from' => 'acaoABC@xgrow.com',
        ];

        $response = $this->putJson("$this->endpoint/{$email->id}?token={$this->token}", $data);
        $response->assertStatus(200);
        $email->refresh();
        $this->assertEquals($data['from'], $email->from);
    }
    
    public function test_delete_email()
    {
        $email = Email::factory()->create();

        $response = $this->deleteJson("$this->endpoint/{$email->id}?token={$this->token}");
        $response->assertStatus(200);
        $this->assertDatabaseMissing('emails', [
            'id' => $email->id
        ]);
    }

}
