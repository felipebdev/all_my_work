<?php

namespace Tests\Feature\Routes\Api\BankAccount;

use Tests\Feature\Helper\JwtWebToken;
use Tests\Feature\Traits\LocalDatabaseIds;
use Tests\TestCase;

class BankAccountRouteTest extends TestCase
{
    use LocalDatabaseIds;

    public function test_bank_account_json()
    {
        $token = JwtWebToken::generateUserToken($this->platformUserId);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->get('/api/bank-account');

        if ($response->getStatusCode() == 409) {
            $this->markTestSkipped('Gateway problem');
        }

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'object',
            'bank_code',
            'agency',
            'agency_digit',
            'account',
            'account_digit',
            'account_type',
            'document_type',
            'document_number',
            'legal_name',
        ]);
    }

    public function test_create_bank_account_with_recipient()
    {
        $token = JwtWebToken::generateUserToken($this->platformUserId);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->post('/api/bank-account', [
                'bank_code' => '001',
                'agency' => '3456',
                'agency_digit' => '',
                'account' => '012345678',
                'account_digit' => '9',
                'account_type' => 'saving',
                'document_number' => '01234567890',
                'legal_name' => 'FORD PREFECT',
            ]);

        if ($response->getStatusCode() == 409) {
            $this->markTestSkipped('Gateway problem');
        }

        $response->assertStatus(200);
    }

    public function test_update_bank_account()
    {
        $token = JwtWebToken::generateUserToken($this->platformUserId);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->put('/api/bank-account', [
                'bank_code' => '001',
                'agency' => '3456',
                'agency_digit' => '',
                'account' => '012345678',
                'account_digit' => '9',
                'account_type' => 'savings',
                'document_number' => '01234567890',
                'legal_name' => 'FORD PREFECT',
            ]);

        if ($response->getStatusCode() == 409) {
            $this->markTestSkipped('Gateway problem');
        }

        $response->assertStatus(200);
    }
}
