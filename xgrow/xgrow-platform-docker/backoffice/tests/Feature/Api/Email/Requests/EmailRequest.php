<?php

namespace Tests\Feature\Api\Email\Requests;

use App\User;
use Illuminate\Http\UploadedFile;
use Tests\Feature\Traits\EmailTrait;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

abstract class EmailRequest extends TestCase
{
    use EmailTrait;
    protected string $token;
    abstract protected function getMethod(): string;
    abstract protected function getEndpoint(): string;

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create();
        $this->token = JWTAuth::fromUser($user);
        $this->app->setLocale('en');
    }

    public function test_area_is_required()
    {
        $response = $this->{$this->getMethod()}(
            "{$this->getEndpoint()}?token={$this->token}",
            [
                'subject' => str_random(3)
            ]
        );
        $response->assertStatus(422);
        $this->assertIsArray($response['errors']);
        $this->assertContains("The area field is required.", $response['errors']['area']);
    }

    public function test_subject_is_required()
    {
        $response = $this->{$this->getMethod()}(
            "{$this->getEndpoint()}?token={$this->token}",
            [
                'area' => 1
            ]
        );
        $response->assertStatus(422);
        $this->assertIsArray($response['errors']);
        $this->assertContains("The subject field is required.", $response['errors']['subject']);
    }

    public function test_subject_shorter_than_3_characters()
    {
        $response = $this->{$this->getMethod()}(
            "{$this->getEndpoint()}?token={$this->token}",
            [
                'subject' => str_random(3),
            ]
        );
        $this->assertArrayNotHasKey('subject', $response['errors']);

        $response = $this->{$this->getMethod()}(
            "{$this->getEndpoint()}?token={$this->token}",
            [
                'subject' => str_random(2)
            ]
        );
        $response->assertStatus(422);
        $this->assertIsArray($response['errors']['subject']);
        $this->assertContains("The subject must be at least 3 characters.", $response['errors']['subject']);
    }

    public function test_message_is_required()
    {
        $response = $this->{$this->getMethod()}(
            "{$this->getEndpoint()}?token={$this->token}",
            [
                'subject' => str_random(3)
            ]
        );
        $response->assertStatus(422);
        $this->assertIsArray($response['errors']['message']);
        $this->assertContains("The message field is required.", $response['errors']['message']);
    }

    public function test_message_shorter_than_3_characters()
    {
        $response = $this->{$this->getMethod()}(
            "{$this->getEndpoint()}?token={$this->token}",
            [
                'message' => str_random(3)
            ]
        );
        $this->assertArrayNotHasKey('message', $response['errors']);

        $response = $this->{$this->getMethod()}(
            "{$this->getEndpoint()}?token={$this->token}",
            [
                'message' => str_random(2)
            ]
        );
        $response->assertStatus(422);
        $this->assertIsArray($response['errors']['message']);
        $this->assertContains("The message must be at least 3 characters.", $response['errors']['message']);
    }

    public function test_from_is_required()
    {
        $response = $this->{$this->getMethod()}(
            "{$this->getEndpoint()}?token={$this->token}",
            [
                'message' => str_random(3)
            ]
        );
        $response->assertStatus(422);
        $this->assertIsArray($response['errors']['from']);
        $this->assertContains("The from field is required.", $response['errors']['from']);
    }

    public function test_from_field_email_is_not_valid()
    {
        $response = $this->{$this->getMethod()}(
            "{$this->getEndpoint()}?token={$this->token}",
            [
                'from' => 'InvalidVail'
            ]
        );
        $response->assertStatus(422);
        $this->assertIsArray($response['errors']['from']);
        $this->assertContains("The from must be a valid email address.", $response['errors']['from']);
    }

}
