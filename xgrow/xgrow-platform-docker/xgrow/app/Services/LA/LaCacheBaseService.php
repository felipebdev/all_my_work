<?php

namespace App\Services\LA;

use Firebase\JWT\JWT;
use GuzzleHttp\Client;

class LaCacheBaseService
{
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('LEARNING_AREA_CACHE_CLEAR_URL', 'https://laapi-develop.xgrow.com/');
    }

    /** LA Token Generator
     * @param string $platformId
     * @return string
     */
    public static function generateToken(string $platformId): string
    {
        $payload = ['platformId' => $platformId];
        $secret = config('jwtplatform.jwt_clean_cache_la') ?? 'secret';
        return JWT::encode($payload, $secret, 'HS256');
    }

    /** Return Guzzle Http Config
     * @param string $platformId
     * @return Client
     */
    public function connectionConfig(string $platformId): Client
    {
        return new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->generateToken($platformId)
            ]
        ]);
    }
}
