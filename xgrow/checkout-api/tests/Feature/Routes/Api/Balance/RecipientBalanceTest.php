<?php

namespace Tests\Feature\Routes\Api\Balance;

use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Http\Response;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Feature\Traits\LocalDatabaseIds;
use Tests\TestCase;

/**
 * Test /api/balance route
 */
class RecipientBalanceTest extends TestCase
{

    use LocalDatabaseIds;

    public function test_listing_transfers_requires_jwt()
    {
        $response = $this->get('/api/balance');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJsonStructure([
                'error',
                'message',
                'response' => [],
            ])
            ->assertJsonPath('error', true)
            ->assertJsonPath('message', 'Token absent');
    }

    public function test_listing_transfers()
    {
        $token = JWT::encode([
            'exp' => Carbon::now()->addMinutes(10)->timestamp,
            'platform_id' => $this->platformId,
            'user_id' => $this->platformUserId,
            'acting_as' => 'client',
        ], env('JWT_WEB'), 'HS256');

        $response = $this->withHeader('Authorization', "Bearer {$token}")->get('/api/balance');

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJson(fn(AssertableJson $json) => $json->whereAllType([
            'object' => 'string',
            'current' => 'integer',
            'available' => 'integer',
            'pending' => 'integer',
            'transferred' => 'integer',
            'anticipation' => 'integer',
        ]));
    }


}
