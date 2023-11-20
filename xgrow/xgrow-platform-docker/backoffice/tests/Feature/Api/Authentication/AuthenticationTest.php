<?php

namespace Tests\Feature\Api\Authentication;

use App\Http\Controllers\Api\AuthApiController;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthenticationTest extends TestCase
{
    private string $endpoint = '/api';
    private string $token;
    private string $user = 'admin@admin.com';
    private string $password = 'admin';

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create(
            [
                'email' => $this->user,
                'password' => Hash::make($this->password)
            ]
        );
        $this->token = JWTAuth::fromUser($user);
    }


    public function test_login_successfully_when_the_two_factor_enabled_is_false()
    {
        $user = User::first();
        $user->two_factor_enabled = 0;
        $user->save();

        $response = $this->postJson(
            "{$this->endpoint}/authenticate",
            [
                'user' => $this->user,
                'password' => $this->password
            ]
        );
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'error', 'message', 'response' => [
                    'user', 'token'
                ]
            ]);
        $this->assertEquals(
            $this->user,
            $response['response']['user']['email']
        );
    }

    public function test_failed_to_login(){
        //no data
        $this->postJson(
            "{$this->endpoint}/authenticate",
            []
        )->assertStatus(400);

        //invalid data
        $this->postJson(
            "{$this->endpoint}/authenticate",
            [
                'user' => $this->user,
                'password' => '123456' //wrong password
            ]
        )->assertStatus(400);
    }

    public function test_generate_two_factor_code(){
        $user = User::first();
        $this->assertNull($user->two_factor_code);
        $this->assertNull($user->two_factor_expires_at);

        $this->postJson(
            "{$this->endpoint}/authenticate",
            [
                'user' => $this->user,
                'password' => $this->password
            ]
        )->assertStatus(200);

        $user = User::first();
        $this->assertNotNull($user->two_factor_code);
        $this->assertNotNull($user->two_factor_expires_at);
    }

    public function test_login_successfully_when_the_two_factor_enabled_is_true(){
        $two_factor_code = '123456';
        $user = User::first();
        $user->two_factor_code = $two_factor_code;
        $user->two_factor_expires_at = Carbon::now()->addDay();
        $user->save();

        $response = $this->postJson(
            "{$this->endpoint}/authenticate",
            [
                'user' => $this->user,
                'password' => $this->password,
                'two_factor_code' => $two_factor_code
            ]
        )->assertStatus(200);
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'error', 'message', 'response' => [
                    'user', 'token'
                ]
            ]);
        $this->assertEquals(
            $this->user,
            $response['response']['user']['email']
        );
    }

    public function test_when_the_two_factor_is_expired(){
        $two_factor_code = '123456';
        $user = User::first();
        $user->two_factor_code = $two_factor_code;
        $user->save();

        //test when two_factor_expires_at is null
        $response = $this->postJson(
            "{$this->endpoint}/authenticate",
            [
                'user' => $this->user,
                'password' => $this->password,
                'two_factor_code' => $two_factor_code
            ]
        )->assertStatus(400);
        $this->assertEquals($response['message'], "Código de verificação expirado");

        //test when two_factor_expires_at is expired
        $user = User::first();
        $user->two_factor_expires_at = Carbon::now()->subDay();
        $user->save();
        $response = $this->postJson(
            "{$this->endpoint}/authenticate",
            [
                'user' => $this->user,
                'password' => $this->password,
                'two_factor_code' => $two_factor_code
            ]
        )->assertStatus(400);
        $this->assertEquals($response['message'], "Código de verificação expirado");
    }


    public function test_failed_with_two_factor_code_expired(){
        $user = User::first();
        $user->two_factor_code = '123456';
        $user->two_factor_expires_at = Carbon::now()->addDay();
        $user->save();
        $response = $this->postJson(
            "{$this->endpoint}/authenticate",
            [
                'user' => $this->user,
                'password' => $this->password,
                'two_factor_code' => 'invalid'
            ]
        )->assertStatus(400);
        $this->assertEquals($response['message'], "Código de verificação inválido");
    }

    public function test_token_validation(){
        $response = $this->postJson("{$this->endpoint}/token-validate?token={$this->token}");
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'error', 'message', 'response' => [
                    'user'
                ]
            ]);
        $this->assertEquals(
            $this->user,
            $response['response']['user']['email']
        );
    }

    public function test_failed_to_token_validation(){
        //no token
        $this->postJson("{$this->endpoint}/token-validate")
                ->assertStatus(401);

        //invalid token
        $this->postJson("{$this->endpoint}/token-validate?token=invalid")
                ->assertStatus(401);
    }

    public function test_logout_successfully(){
        $response = $this->postJson("{$this->endpoint}/token-logout?token={$this->token}");
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'error', 'message', 'response'
            ]);
        $this->assertEquals(
            'Ok',
            $response['message']
        );
    }

    public function test_failed_to_logout(){
        //no token
        $this->postJson("{$this->endpoint}/token-logout")
            ->assertStatus(401);

        //invalid token
        $this->postJson("{$this->endpoint}/token-logout?token=invalid")
            ->assertStatus(401);
    }

    public function test_check_email_not_found_when_generation_new_password(){
        $user = User::first();
        $originalPassword = $user->password;

        //no email
        $this->getJson("{$this->endpoint}/new-random-password")
            ->assertStatus(400);

        //invalid email
        $this->getJson("{$this->endpoint}/new-random-password?email=invalid")
            ->assertStatus(400);

        //test if it didn't generate new random password
        $user = User::first();
        $this->assertEquals($originalPassword, $user->password);
    }


    public function test_if_it_generates_new_random_password(){
        $user = User::first();
        $originalPassword = $user->password;

        $this->getJson("{$this->endpoint}/new-random-password?email=admin@admin.com")
            ->assertStatus(200);

        $user = User::first();
        $this->assertNotEquals($originalPassword, $user->password);
    }

}
