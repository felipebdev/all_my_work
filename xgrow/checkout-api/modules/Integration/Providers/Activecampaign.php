<?php

namespace Modules\Integration\Providers;

use Exception;
use GuzzleHttp\Client as HttpClient;

class Activecampaign extends BaseProvider
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

    public function lists()
    {
        try {
            $apiRequest = $this->client->request(
                'GET',
                "{$this->apiWebhook}/api/3/lists",
                ['headers' => ['Api-Token' => $this->apiKey]]
            );

            return json_decode($apiRequest->getBody()) ?? [];
        } catch (Exception $e) {
            return [];
        }
    }

    public function tags()
    {
        try {
            $apiRequest = $this->client->request(
                'GET',
                "{$this->apiWebhook}/api/3/tags",
                ['headers' => ['Api-Token' => $this->apiKey]]
            );

            return json_decode($apiRequest->getBody()) ?? [];
        } catch (Exception $e) {
            return [];
        }
    }
}
