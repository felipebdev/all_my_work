<?php

namespace Modules\Integration\Providers;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class Hubspot extends BaseProvider
{

    private const HUBSPOT_LISTS = 'https://api.hubapi.com/contacts/v1/lists';

    public function lists(): array
    {
        $collection = collect([]);
        $loopCount = 0;
        $limit = 250; // maximum allowed on HubSpot

        try {
            do {
                $offset = $loopCount * $limit;
                $data = $this->doRequest('GET', self::HUBSPOT_LISTS, $limit, $offset);

                $collection = $collection->merge($data['lists'] ?? []);
                $loopCount++;
            } while ($data['has-more'] ?? false);

            return [
                'lists' => $collection->toArray(),
            ];
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * @param  string  $method
     * @param  string  $endpoint
     * @param  int  $limit
     * @param  int  $offset
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function doRequest(string $method, string $endpoint, int $limit, int $offset): array
    {
        $apiRequest = (new Client())->request($method, $endpoint, [
            RequestOptions::QUERY => [
                'hapikey' => $this->apiKey,
                'limit' => $limit,
                'offset' => $offset,
            ],
        ]);

        return json_decode($apiRequest->getBody(), $associative = true) ?? [];
    }

}
