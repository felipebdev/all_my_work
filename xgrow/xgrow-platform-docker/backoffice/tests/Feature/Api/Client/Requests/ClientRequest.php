<?php

namespace Tests\Feature\Api\Client\Requests;

use App\User;
use Illuminate\Http\UploadedFile;
use Tests\Feature\Traits\ClientTrait;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

abstract class ClientRequest extends TestCase
{
    use ClientTrait;
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

    public function test_first_name_is_required()
    {
        $response = $this->{$this->getMethod()}(
            "{$this->getEndpoint()}?token={$this->token}",
            ['last_name' => 'ok']);
        $response->assertStatus(422);
        $this->assertIsArray($response['errors']['first_name']);
        $this->assertContains("The first name field is required.", $response['errors']['first_name']);
    }

    public function test_first_name_is_shorter_than_191_characters()
    {
        $response = $this->{$this->getMethod()}(
            "{$this->getEndpoint()}?token={$this->token}",
            [
                'first_name' => str_random(191)
            ]
        );
        $this->assertArrayNotHasKey('first_name', $response['errors']);

        $response = $this->{$this->getMethod()}(
            "{$this->getEndpoint()}?token={$this->token}",
            [
                'first_name' => str_random(192)
            ]
        );
        $response->assertStatus(422);
        $this->assertIsArray($response['errors']['first_name']);
        $this->assertContains("The first name may not be greater than 191 characters.", $response['errors']['first_name']);
    }

    public function test_last_name_is_required()
    {
        $response = $this->{$this->getMethod()}(
            "{$this->getEndpoint()}?token={$this->token}");
        $response->assertStatus(422);
        $this->assertIsArray($response['errors']['last_name']);
        $this->assertContains("The last name field is required.", $response['errors']['last_name']);
    }

    public function test_last_name_is_shorter_than_191_characters()
    {
        $response = $this->{$this->getMethod()}(
            "{$this->getEndpoint()}?token={$this->token}",
            [
                'last_name' => str_random(191)
            ]
        );
        $this->assertArrayNotHasKey('last_name', $response['errors']);

        $response = $this->{$this->getMethod()}(
            "{$this->getEndpoint()}?token={$this->token}",
            [
                'last_name' => str_random(192)
            ]
        );
        $response->assertStatus(422);
        $this->assertIsArray($response['errors']['last_name']);
        $this->assertContains("The last name may not be greater than 191 characters.", $response['errors']['last_name']);
    }

    public function test_email_is_not_valid()
    {
        $response = $this->{$this->getMethod()}(
            "{$this->getEndpoint()}?token={$this->token}",
            ['email' => 'isNotValidEmail']
        );
        $response->assertStatus(422);
        $this->assertIsArray($response['errors']['email']);
        $this->assertContains("The email must be a valid email address.", $response['errors']['email']);
    }

    public function test_email_has_already_been_taken()
    {
        $this->createClients();
        $response = $this->{$this->getMethod()}(
            "{$this->getEndpoint()}?token={$this->token}",
            ['email' => 'first_client@xgrow.com']
        );
        $response->assertStatus(422);
        $this->assertIsArray($response['errors']['email']);
        $this->assertContains("The email has already been taken.", $response['errors']['email']);
    }

    public function test_password_is_not_valid()
    {
        $response = $this->{$this->getMethod()}(
            "{$this->getEndpoint()}?token={$this->token}",
            ['password' => '123456']
        );
        $response->assertStatus(422);
        $this->assertContains("The password must contain at least one letter.", $response['errors']['password']);
        $this->assertContains("The password must contain at least one symbol.", $response['errors']['password']);
    }

    public function test_password_must_be_at_least_5_characters()
    {
        $response = $this->{$this->getMethod()}(
            "{$this->getEndpoint()}?token={$this->token}",
            [
                'password' => str_random(4)
            ]
        );
        $response->assertStatus(422);
        $this->assertContains("The password must be at least 5 characters.", $response['errors']['password']);
    }

    public function test_password_does_not_match()
    {
        $response = $this->{$this->getMethod()}(
            "{$this->getEndpoint()}?token={$this->token}",
            [
                'password' => 'abc-123?',
                'password_confirmation' => 'www'
            ]
        );
        $response->assertStatus(422);
        $this->assertContains("The password confirmation does not match.", $response['errors']['password']);
    }

    public function test_type_person_is_required()
    {
        $response = $this->{$this->getMethod()}(
            "{$this->getEndpoint()}?token={$this->token}"
        );
        $response->assertStatus(422);
        $this->assertIsArray($response['errors']['type_person']);
        $this->assertContains("The type person field is required.", $response['errors']['type_person']);
    }

    public function test_type_person_is_not_valid()
    {
        $response = $this->{$this->getMethod()}(
            "{$this->getEndpoint()}?token={$this->token}",
            [

                'type_person' => 'X'
            ]
        );
        $response->assertStatus(422);
        $this->assertIsArray($response['errors']['type_person']);
        $this->assertContains("The selected type person is invalid.", $response['errors']['type_person']);
    }


    public function test_percent_split_is_required()
    {
        $response = $this->{$this->getMethod()}(
            "{$this->getEndpoint()}?token={$this->token}"
        );
        $response->assertStatus(422);
        $this->assertIsArray($response['errors']['percent_split']);
        $this->assertContains("The percent split field is required.", $response['errors']['percent_split']);
    }

    public function test_percent_split_is_not_valid()
    {
        $response = $this->{$this->getMethod()}(
            "{$this->getEndpoint()}?token={$this->token}",
            [

                'percent_split' => 'X'
            ]
        );
        $response->assertStatus(422);
        $this->assertIsArray($response['errors']['percent_split']);
        $this->assertContains("The percent split must be a number.", $response['errors']['percent_split']);
    }

    public function test_tax_transaction_is_required()
    {
        $response = $this->{$this->getMethod()}(
            "{$this->getEndpoint()}?token={$this->token}"
        );
        $response->assertStatus(422);
        $this->assertIsArray($response['errors']['tax_transaction']);
        $this->assertContains("The tax transaction field is required.", $response['errors']['tax_transaction']);
    }

    public function test_tax_transaction_is_not_valid()
    {
        $response = $this->{$this->getMethod()}(
            "{$this->getEndpoint()}?token={$this->token}",
            [

                'tax_transaction' => 'X'
            ]
        );
        $response->assertStatus(422);
        $this->assertIsArray($response['errors']['tax_transaction']);
        $this->assertContains("The tax transaction must be a number.", $response['errors']['tax_transaction']);
    }


    public function test_image_is_not_valid()
    {

        $image = UploadedFile::fake()->image('fake.csv');

        $response = $this->{$this->getMethod()}(
            "{$this->getEndpoint()}?token={$this->token}",
            ['image' => $image]
        );

        $response->assertStatus(422);
        $this->assertIsArray($response['errors']['image']);
        $this->assertContains("The image must be a file of type: png, jpg, jpeg.", $response['errors']['image']);
    }

}
