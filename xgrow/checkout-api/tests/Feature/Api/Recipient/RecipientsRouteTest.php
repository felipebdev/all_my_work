<?php

namespace Tests\Feature\Api\Recipient;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Feature\Helper\JwtWebToken;
use Tests\Feature\Traits\LocalDatabaseIds;
use Tests\TestCase;

class RecipientsRouteTest extends TestCase
{
    use LocalDatabaseIds;
    use DatabaseTransactions;

    public function test_can_create_recipient_via_endpoint()
    {
        DB::table('platforms')
            ->where('id', $this->platformId)
            ->update(['recipient_id' => null]); // unset recipient_id

        $jwt = JwtWebToken::generateToken($this->platformId, $this->platformUserId, [
            'acting_as' => 'client'
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$jwt)
            ->post('/api/recipients');

        if ($response->getStatusCode() == 409) {
            $this->markTestSkipped('Gateway problem');
        }

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) {
            return $json->where('object', 'recipient')
                ->whereType('id', 'string')
                ->whereType('name', 'string')
                ->whereType('email', 'string')
                ->whereType('type', 'string')
                ->whereType('status', 'string')
                ->whereType('reason', ['string', 'null'])
                ->whereType('can_transact', 'boolean');
        });
    }


}
