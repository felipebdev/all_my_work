<?php

namespace App\Services;

use Exception;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\Facades\Session;
use stdClass;
use Throwable;

class LAService
{
    const SESSION_NAME = 'api-la-producer-token';

    /**
     * @var GuzzleHttp\Client
     */
    private $client;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var string
     */
    private $bearerToken;

    /**
     * @var 
     */
    private $headers;

    public function __construct(
        string $platformId,
        string $producerId
    ) {
        $this->client = new HttpClient([
            'base_uri' => env('LA_PLATFORM_CONFIGURATION_API', 'https://la-config-develop.xgrow.com/v1/api'),
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);

        $this->baseUrl = env('LA_PLATFORM_CONFIGURATION_API', 'https://la-config-develop.xgrow.com/v1/api');
        $this->bearerToken = $this->setBearerToken($platformId, $producerId);
        $this->headers = $this->setHeaders();
    }

    /**
     * @see https://documenter.getpostman.com/view/16063676/TzsZpnJt#97a8f82f-0969-452d-896d-da92195de17a
     *
     * @param string $platformId
     * @param string $producerId
     * @return object
     */
    protected function generateProducerToken(
        string $platformId,
        string $producerId
    ): object {
        try {
            $body = json_encode([
                'platformId' => $platformId,
                'producerId' => $producerId
            ]);

            $response = $this->client->request(
                'POST',
                "{$this->baseUrl}/producer/auth",
                ['body' => $body]
            );

            return json_decode($response->getBody()) ?? [];
        } catch (Exception $e) {
            new Throwable('Can not generate token');
        }
    }

    /**
     * @see https://documenter.getpostman.com/view/16063676/TzsZpnJt#24998ed4-3724-4b43-bc59-d249a65fd0d3
     * @return array
     */
    public function listBlockedAccesses()
    {
        try {
            $response = $this->client->request(
                'GET',
                "{$this->baseUrl}/producer/accesses/blocked",
                ['headers' => $this->headers]
            );

            return json_decode($response->getBody()) ?? [];
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * @see https://documenter.getpostman.com/view/16063676/TzsZpnJt#10bd17aa-9f31-44ac-8989-8d292c496f12
     * @param int $userId
     * @param int $action
     * @return bool
     */
    public function updateBlockedAccess(int $userId, int $action): bool
    {
        $body = json_encode([
            'userId' => $userId,
            'action' => $action
        ]);

        try {
            $response = $this->client->request(
                'PUT',
                "{$this->baseUrl}/producer/accesses/blocked",
                [
                    'headers' => $this->headers,
                    'body'    => $body
                ]
            );

            return $response->getBody()->data;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param string $platformId
     * @param string $producerId
     * @return string
     */
    private function setBearerToken(
        string $platformId,
        string $producerId
    ): string {
        $now = strtotime(date('Y-m-d H:i:s'));
        $token = (
            Session::has(self::SESSION_NAME) 
            ? json_decode(Session::get(self::SESSION_NAME))
            : null
        );

        if (
            empty($token)
            || !property_exists($token, 'token')
            || !property_exists($token, 'expires_in')
            || empty($token->token)
            || empty($token->expires_in)
            || ($now >= $token->expires_in)
        ) {
            $bearerToken = $this->generateProducerToken($platformId, $producerId);
            $token = $this->setSession($bearerToken->token);
        } 

        return $token->token;
    }

    /**
     * @param string $bearerToken
     * @return object
     */
    private function setSession(string $bearerToken): object {
        $expiresIn = strtotime(date('Y-m-d H:i:s')) + 60*60; //now + 1h
        $token = ['token' => $bearerToken, 'expires_in' => $expiresIn];
        Session::put(self::SESSION_NAME, json_encode($token));
        return (object) $token;
    }

    /**
     * @return array
     */
    private function setHeaders(): array
    {
        return [
            'Authorization' => "Bearer {$this->bearerToken}",
        ];
    }
}
