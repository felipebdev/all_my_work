<?php

namespace App\Services\LA;

use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class LaProducerBaseService
{
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('learningarea.url_config');
    }

    /** LA Token Generator
     * @param string $platformId
     * @param $producerId
     * @param string $role
     * @return string
     * @throws GuzzleException
     */
    public function generateToken(string $platformId, $producerId, string $role = '1111111'): string
    {
        $client = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'x-platform-xgrow' => config('learningarea.url_config_token')
            ]
        ]);

        $res = $client->post("auth/producer", ['json' => ['platformId' => $platformId, 'producerId' => $producerId, $role => '111111']]);

        return json_decode($res->getBody()->getContents())->token;
    }

    /** Return Guzzle Http Config
     * @param string $platformId
     * @param $producerId
     * @return Client
     * @throws GuzzleException
     */
    public function connectionConfig(string $platformId, $producerId): Client
    {
        return new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->generateToken($platformId, $producerId)
            ]
        ]);
    }

    /**
     * $laConnection = (new LaProducerBaseService)->connectionConfig(Auth::user()->platform_id, Auth::user()->id);
     * dd($laConnection->get('/v1/api/auth/producer')->getBody()->getContents());*/
}
