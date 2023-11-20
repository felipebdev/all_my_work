<?php

namespace Modules\Integration\Providers;

use Exception;
use GuzzleHttp\Client as HttpClient;

class Mailchimp extends BaseProvider
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
            $baseUrl = $this->getBaseUrl();
            $apiRequest = $this->client->request(
                'GET',
                "{$baseUrl}/lists",
                ['headers' => ['Authorization' => "Bearer {$this->apiKey}"]]
            );

            return json_decode($apiRequest->getBody()) ?? [];
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * @todo
     */
    public function tags(array $params)
    {
        try {
            $baseUrl = $this->getBaseUrl();
            $listId = $params['listId'];
            $apiRequest = $this->client->request(
                'GET',
                "{$baseUrl}/lists/{$listId}/tag-search",
                ['headers' => ['Authorization' => "Bearer {$this->apiKey}"]]
            );

            return json_decode($apiRequest->getBody()) ?? [];
        } catch (Exception $e) {
            return [];
        }
    }

    private function getBaseUrl()
    {
        list($token, $region) = explode('-', $this->apiKey);
        return "https://{$region}.api.mailchimp.com/3.0";
    }
}
