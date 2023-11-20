<?php

namespace App\Services\LA;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Middleware;
use GuzzleHttp\RequestOptions;

class ContentAPIService
{
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('learningarea.url_config');
    }

    /** LA Token Generator
     * @param string $platformId
     * @param $producerId
     * @param string $roles
     * @return string
     */
    public function generateToken(string $platformId, $producerId, string $roles = '1111111')
    {
        $curl = curl_init();

        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
            'x-platform-xgrow: ' . config('learningarea.url_config_token')
        ];

        $post = [
            'platformId' => $platformId,
            'producerId' => $producerId,
            'roles' => $roles
        ];

        $json = json_encode($post);

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->baseUrl . "/auth/producer",
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $json
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response)->token;
    }

    /** Return Guzzle Http Config
     * @param string $platformId
     * @param $producerId
     * @param $roles
     * @return Client
     * @throws GuzzleException
     */
    public function connectionConfig(string $platformId, $producerId, $roles): Client
    {
        return new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->generateToken($platformId, strval($producerId), $roles)
            ]
        ]);
    }
}
