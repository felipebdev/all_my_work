<?php

namespace App\Services;

use Exception;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

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
    protected $headers;

    public function __construct(
        string $platformId,
        string $producerId
    ) {
        $this->client = new HttpClient([
            'base_uri' => env('LA_PLATFORM_CONFIGURATION_API', 'https://la-config-develop.xgrow.com/v1/api'),
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'x-platform-xgrow' => config('learningarea.url_config_token')
            ]
        ]);

        $this->baseUrl = env('LA_PLATFORM_CONFIGURATION_API', 'https://la-config-develop.xgrow.com/v1/api');
        //dd($this->baseUrl);
        $this->bearerToken = $this->setBearerToken($platformId, $producerId);
        $this->headers = $this->setHeaders();
    }

    /**
     * @see https://documenter.getpostman.com/view/16063676/TzsZpnJt#97a8f82f-0969-452d-896d-da92195de17a
     *
     * @param string $platformId
     * @param string $producerId
     * @return false|object|string
     */
    protected function generateProducerToken(
        string $platformId,
        string $producerId
    ) {
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
            return json_encode($e->getMessage()) ?? '';
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
                    'body' => $body
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
        $token = (Session::has(self::SESSION_NAME)
            ? json_decode(Session::get(self::SESSION_NAME))
            : null);

        if (
            empty($token)
            || !property_exists($token, 'token')
            || !property_exists($token, 'expires_in')
            || !property_exists($token, 'platform_id')
            || empty($token->token)
            || empty($token->expires_in)
            || empty($token->platform_id)
            || $token->platform_id != $platformId
            || ($now >= $token->expires_in)
        ) {
            $bearerToken = $this->generateProducerToken($platformId, $producerId);
            $token = $this->setSession($bearerToken->token, $platformId);
        }

        return $token->token;
    }

    /**
     * @param string $bearerToken
     * @return object
     */
    private function setSession(string $bearerToken, string $platformId): object
    {
        $expiresIn = strtotime(date('Y-m-d H:i:s')) + 60 * 60; //now + 1h
        $token = ['token' => $bearerToken, 'expires_in' => $expiresIn, 'platform_id' => $platformId];
        Session::put(self::SESSION_NAME, json_encode($token));
        return (object)$token;
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

    /**
     * @return array
     */
    protected function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @return array|mixed
     * @throws GuzzleException
     */
    public function getOnlineUsers()
    {
        try {

            $response = $this->client->request(
                'GET',
                "{$this->baseUrl}/logs/online/data",
                ['headers' => $this->headers]
            );

            return json_decode($response->getBody()) ?? [];
        } catch (Exception $e) {
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    /**
     * @return array|mixed
     * @throws GuzzleException
     */
    public function getTotalOnlineUsers()
    {
        try {

            $response = $this->client->request(
                'GET',
                "{$this->baseUrl}/logs/online",
                ['headers' => $this->headers]
            );

            return json_decode($response->getBody()) ?? [];
        } catch (Exception $e) {
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    /**
     * Generic get Method
     * @param $url
     * @return array|mixed
     * @throws GuzzleException
     */
    public function get($url, $data = null)
    {
        try {
            $response = $this->client->request(
                'GET',
                "{$this->baseUrl}{$url}",
                [
                    'headers' => $this->headers,
                    'json' => $data
                ]
            );

            return json_decode($response->getBody());
        } catch (Exception $e) {
            return [];
        }
    }

    /** Generic Post (form data and json)
     * Use json_encode if need send json on body
     * Generic post Method
     * @param $url
     * @param $data
     * @return array|mixed
     * @throws GuzzleException
     */
    public function post($url, $data)
    {
        try {
            $response = $this->client->request(
                'POST',
                "{$this->baseUrl}{$url}",
                [
                    'headers' => $this->headers,
                    'body' => $data
                ]
            );

            return json_decode($response->getBody()) ?? [];
        } catch (BadResponseException $e) {
            return ['error' => true, 'message' => $e->getResponse()->getBody()->getContents()];
        }
    }

    /** Generic DELETE (form data and json)
     * Use json_encode if need send json on body
     * @param $url
     * @param $data
     * @return array
     * @throws GuzzleException
     */
    public function delete($url, $data): array
    {
        try {
            $response = $this->client->request(
                'DELETE',
                "{$this->baseUrl}{$url}",
                [
                    'headers' => $this->headers,
                    'body' => $data
                ]
            );
            $resBody =  $response->getBody();
            return (isset($resBody->data)) ? $resBody->data : [];
        } catch (BadResponseException $e) {
            return ['error' => true, 'message' => $e->getResponse()->getBody()->getContents()];
        }
    }


    /** Generic PUT (form data and json)
     * Use json_encode if need send json on body
     * @param $url
     * @param $data
     * @return array|mixed
     * @throws GuzzleException
     */
    public function put($url, $data)
    {
        try {
            $response = $this->client->request(
                'PUT',
                "{$this->baseUrl}{$url}",
                [
                    'headers' => $this->headers,
                    'body' => $data
                ]
            );

            return json_decode($response->getBody()) ?? [];
        } catch (BadResponseException $e) {
            return ['error' => true, 'message' => $e->getResponse()->getBody()->getContents()];
        }
    }

    /**
     * @param $data
     * @return array
     */
    public function postStartLA($data): array
    {
        try {

            $response = $this->client->request(
                'POST',
                "{$this->baseUrl}/theme",
                [
                    'headers' => $this->headers,
                    'json' => $data
                ]
            );

            Log::info("Tema criado com sucesso! " . $response->getBody());

            return ['error' => false, 'message' => 'Tema criado com sucesso! ', 'status' => $response->getStatusCode()];
        } catch (GuzzleException $e) {

            Log::error("Não foi possível cadastrar o tema default para a plataforma " . $e->getMessage());

            return ['error' => true, 'message' => $e->getMessage()];
        }
    }
}
