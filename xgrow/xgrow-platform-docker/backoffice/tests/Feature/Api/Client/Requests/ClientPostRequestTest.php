<?php

namespace Tests\Feature\Api\Client\Requests;

class ClientPostRequestTest extends ClientRequest
{
    protected string $method = 'postJson';
    protected string $endpoint = '/api/client';

    protected function getMethod(): string
    {
        return $this->method;
    }

    protected function getEndpoint(): string
    {
        return $this->endpoint;
    }

    public function test_password_is_required()
    {
        $response = $this->{$this->getMethod()}(
            "{$this->getEndpoint()}?token={$this->token}");
        $response->assertStatus(422);
        $this->assertIsArray($response['errors']['password']);
        $this->assertContains("The password field is required.", $response['errors']['password']);
    }

    public function test_email_is_required()
    {
        $response = $this->postJson(
            "{$this->getEndpoint()}?token={$this->token}");
        $response->assertStatus(422);
        $this->assertIsArray($response['errors']['email']);
        $this->assertContains("The email field is required.", $response['errors']['email']);
    }
}
