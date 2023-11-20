<?php

namespace Modules\Integration\Providers;

use Exception;
use GuzzleHttp\Client as HttpClient;

class Mautic extends BaseProvider
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

    public function lists(): array
    {
        try {
            $apiRequest = $this->client->request(
                'GET',
                "{$this->apiWebhook}/api/segments",
                ['headers' => ['Authorization' => "Basic " . base64_encode($this->apiAccount . ':' . $this->apiKey)]]
            );

            $data = json_decode($apiRequest->getBody());

            $collection = $data->lists ?? [];

            return [
                'lists' => $collection,
            ];
        } catch (Exception $e) {
            return [];
        }
    }
}
