<?php

namespace Modules\Integration\Providers;

use Exception;
use GuzzleHttp\Client as HttpClient;

class Leadlovers extends BaseProvider
{
    /**
     * @var GuzzleHttp\Client
     */
    private $client;

    public function __construct()
    {
        $this->client = new HttpClient();
        return $this;
    }

    public function machines()
    {
        try {
            $baseUrl = $this->getBaseUrl();
            $apiRequest = $this->client->request(
                'GET',
                "{$baseUrl}/Machines",
                ['query' => ['token' => $this->apiKey]]
            );

            return json_decode($apiRequest->getBody()) ?? [];
        } catch (Exception $e) {
            return [];
        }
    }

    public function emailSequences(array $params)
    {
        try {
            $baseUrl = $this->getBaseUrl();
            $machineCode = $params['machineCode'];
            $apiRequest = $this->client->request(
                'GET',
                "{$baseUrl}/EmailSequences",
                ['query' => [
                    'token' => $this->apiKey,
                    'machineCode' => $machineCode
                    ]
                ]
            );

            return json_decode($apiRequest->getBody()) ?? [];
        } catch (Exception $e) {
            return [];
        }
    }

    public function levels(array $params)
    {
        try {
            $baseUrl = $this->getBaseUrl();
            $machineCode = $params['machineCode'];
            $emailSequenceCode = $params['emailSequenceCode'];
            $apiRequest = $this->client->request(
                'GET',
                "{$baseUrl}/Levels",
                ['query' => [
                    'token' => $this->apiKey,
                    'machineCode' => $machineCode,
                    'sequenceCode' => $emailSequenceCode
                    ]
                ]
            );

            return json_decode($apiRequest->getBody()) ?? [];
        } catch (Exception $e) {
            return [];
        }
    }

    public function tags()
    {
        try {
            $baseUrl = $this->getBaseUrl();
            $apiRequest = $this->client->request(
                'GET',
                "{$baseUrl}/Tags",
                ['query' => ['token' => $this->apiKey]]
            );

            return json_decode($apiRequest->getBody()) ?? [];
        } catch (Exception $e) {
            return [];
        }
    }

    private function getBaseUrl()
    {
        return "https://llapi.leadlovers.com/webapi";
    }
}
