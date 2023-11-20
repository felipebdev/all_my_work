<?php

namespace Tests\Feature\Api\Platform\Requests;

namespace Tests\Feature\Api\Platform\Requests;

use App\Client;
use App\Platform;
use App\User;
use Illuminate\Http\UploadedFile;
use Tests\Feature\Traits\PlatformTrait;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

abstract class PlatformRequest extends TestCase
{
    use PlatformTrait;

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

    public function test_name_is_required()
    {
        $response = $this->{$this->getMethod()}("{$this->getEndpoint()}?token={$this->token}",[]);
        $response->assertStatus(422);
        $this->assertIsArray($response['errors']['name']);
        $this->assertContains("The platform name field is required.", $response['errors']['name']);
    }

    public function test_if_name_has_already_been_taken()
    {
        $data = ['name' => 'Platform'];
        Client::factory()->create();
        Platform::factory()->create($data);
        $response = $this->{$this->getMethod()}("{$this->getEndpoint()}?token={$this->token}",$data);
        $response->assertStatus(422);
        $this->assertIsArray($response['errors']['name']);
        $this->assertContains("The platform name has already been taken.", $response['errors']['name']);
    }

    public function test_url_is_required()
    {
        $response = $this->{$this->getMethod()}("{$this->getEndpoint()}?token={$this->token}",[]);
        $response->assertStatus(422);
        $this->assertIsArray($response['errors']['url']);
        $this->assertContains("The platform URL field is required.", $response['errors']['url']);
    }

    public function test_if_url_has_already_been_taken()
    {
        $data = ['url' => 'http://www.mexico.com'];
        Client::factory()->create();
        Platform::factory()->create($data);
        $response = $this->{$this->getMethod()}("{$this->getEndpoint()}?token={$this->token}",$data);
        $response->assertStatus(422);
        $this->assertIsArray($response['errors']['url']);
        $this->assertContains("The platform URL has already been taken.", $response['errors']['url']);
    }

    public function test_slug_is_nullable()
    {
        $response = $this->{$this->getMethod()}("{$this->getEndpoint()}?token={$this->token}",[]);
        $response->assertStatus(422);
        $this->assertArrayNotHasKey('slug', $response['errors']);
    }

    public function test_if_slug_has_already_been_taken()
    {
        $data = ['slug' => 'platform-name'];
        Client::factory()->create();
        Platform::factory()->create($data);
        //$data['slug'] = str_replace($data['slug'], '-', ' ');
        $response = $this->{$this->getMethod()}("{$this->getEndpoint()}?token={$this->token}",$data);
        $response->assertStatus(422);
        $this->assertIsArray($response['errors']['slug']);
        $this->assertContains("The recommended address has already been taken.", $response['errors']['slug']);
    }

    public function test_ips_available_is_required_if_restrict_ips_is_true()
    {
        $response = $this->{$this->getMethod()}("{$this->getEndpoint()}?token={$this->token}",[
            'restrict_ips' => 'true'
        ]);
        $response->assertStatus(422);
        $this->assertIsArray($response['errors']['ips_available']);
        $this->assertContains("The IP's field is required when IP's list is true.", $response['errors']['ips_available']);
    }

    public function test_customer_id_is_required()
    {
        $response = $this->{$this->getMethod()}("{$this->getEndpoint()}?token={$this->token}",[]);
        $response->assertStatus(422);
        $this->assertIsArray($response['errors']['customer_id']);
        $this->assertContains("The customer id field is required.", $response['errors']['customer_id']);
    }

    public function test_customer_id_is_not_numeric()
    {
        $response = $this->{$this->getMethod()}("{$this->getEndpoint()}?token={$this->token}",[
            'customer_id' => 'x'
        ]);
        $response->assertStatus(422);
        $this->assertIsArray($response['errors']['customer_id']);
        $this->assertContains("The customer id must be a number.", $response['errors']['customer_id']);
    }

    public function test_customer_id_is_invalid()
    {
        $response = $this->{$this->getMethod()}("{$this->getEndpoint()}?token={$this->token}",[
            'customer_id' => 1
        ]);
        $response->assertStatus(422);
        $this->assertIsArray($response['errors']['customer_id']);
        $this->assertContains("The selected customer id is invalid.", $response['errors']['customer_id']);
    }

    public function test_customer_id_is_valid()
    {
        $client = Client::factory()->create();
        $response = $this->{$this->getMethod()}("{$this->getEndpoint()}?token={$this->token}",[
            'customer_id' => $client->id
        ]);
        $response->assertStatus(422);
        $this->assertArrayNotHasKey('customer_id', $response['errors']);
    }

    public function test_name_slug_is_required()
    {
        $response = $this->{$this->getMethod()}("{$this->getEndpoint()}?token={$this->token}",[]);
        $response->assertStatus(422);
        $this->assertIsArray($response['errors']['name_slug']);
        $this->assertContains("The recommended address slug field is required.", $response['errors']['name_slug']);
    }

    public function test_image_is_not_valid()
    {

        $image = UploadedFile::fake()->image('fake.csv');

        $response = $this->{$this->getMethod()}(
            "{$this->getEndpoint()}?token={$this->token}",
            ['cover' => $image]
        );

        $response->assertStatus(422);
        $this->assertIsArray($response['errors']['cover']);
        $this->assertContains("The platform image must be an image.", $response['errors']['cover']);
    }

}
