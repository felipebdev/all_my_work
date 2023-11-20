<?php

namespace Modules\Integration\Providers;

use Exception;
use Infusionsoft\Token;
use Modules\Integration\Contracts\IOAuthable;
use Modules\Integration\Enums\TypeEnum;
use Modules\Integration\Models\OAuthToken;
use GuzzleHttp\Client as HttpClient;

class Rdstation extends BaseProvider implements IOAuthable
{
    private $client;
    private $clientId;
    private $clientSecret;

    public function __construct()
    {
        $this->client = new HttpClient();
        $this->clientId = config('apps.services.rdstation.app_id');
        $this->clientSecret = config('apps.services.rdstation.app_secret');
        return $this;
    }

    public function provider(): string
    {
        return TypeEnum::RDSTATION;
    }

    public function accessToken(string $code): OAuthToken
    {
        try {
            $apiRequest = $this->client->request(
                'POST', 
                'https://api.rd.services/auth/token', 
                [
                    'form_params' => [
                        'code' => $code,
                        'client_id' => $this->clientId,
                        'client_secret' => $this->clientSecret
                    ]
                ] 
            );
            $response = json_decode($apiRequest->getBody()) ?? [];
            
            return new OAuthToken(
                $response->access_token,
                $response->refresh_token,
                $response->expires_in
            );

        } catch (Exception $e) {
            return [];
        }
    }

    public function refreshToken(
        string $accessToken,
        string $refreshToken,
        int $expiresIn
    ): OAuthToken {
        try {
            $apiRequest = $this->client->request(
                'POST', 
                'https://api.rd.services/auth/token', 
                [
                    'form_params' => [
                        'client_id' => $this->clientId,
                        'client_secret' => $this->clientSecret,
                        'refresh_token' => $refreshToken
                    ]
                ] 
            );
            $response = json_decode($apiRequest->getBody()) ?? [];

            return new OAuthToken(
                $response->accessToken,
                $response->refreshToken,
                $response->expiresIn
            );

        } catch (Exception $e) {
            return [];
        }
    }
}
