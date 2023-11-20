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
            $collection = collect([]);
            $loopCount = 0;
            $limit = 100;
            do {
                $offset = $loopCount * $limit;

                $apiRequest = $this->client->request(
                    'GET',
                    "{$this->apiWebhook}/api/3/lists?limit={$limit}&offset={$offset}",
                    ['headers' => ['Api-Token' => $this->apiKey]]
                );

                $data = json_decode($apiRequest->getBody());
                $total = (int) $data->meta->total ?? 0;

                $collection = $collection->merge($data->lists ?? []);
                $loopCount++;
            } while ($collection->count() < $total);

            return [
                'lists' => $collection->toArray(),
                'meta' => [
                    'total' => $total
                ]
            ];
        } catch (Exception $e) {
            return [];
        }
    }

    public function tags()
    {
        try {
            $collection = collect([]);
            $loopCount = 0;
            $limit = 100;
            do {
                $offset = $loopCount * $limit;

                $apiRequest = $this->client->request(
                    'GET',
                    "{$this->apiWebhook}/api/3/tags?limit={$limit}&offset={$offset}",
                    ['headers' => ['Api-Token' => $this->apiKey]]
                );
                $data = json_decode($apiRequest->getBody());
                $total = (int)$data->meta->total ?? 0;
                $collection = $collection->merge($data->tags ?? []);
                $loopCount++;
            } while ($collection->count() < $total);
            return [
                'tags' => $collection->toArray(),
                'meta' => [
                    'total' => $total
                ]
            ];
        } catch (Exception $e) {
            return [];
        }
    }

    public function customFields()
    {
        try {
            $collection = collect([]);
            $loopCount = 0;
            do {
                $apiRequest = $this->client->request(
                    'GET',
                    "{$this->apiWebhook}/api/3/fields",
                    ['headers' => ['Api-Token' => $this->apiKey]]
                );
                $data = json_decode($apiRequest->getBody());
                $total = count($data->fields) ?? 0;
                $collection = $collection->merge($data->fields ?? []);
                $collection = $collection->map(function ($field) {
                    return ['id' => $field->id, 'title' => $field->title];
                });
                $loopCount++;
            } while ($collection->count() < $total);
            return [
                'fields' => $collection->toArray(),
                'meta' => [
                    'total' => $total
                ]
            ];
        } catch (Exception $e) {
            return [];
        }
    }
}
