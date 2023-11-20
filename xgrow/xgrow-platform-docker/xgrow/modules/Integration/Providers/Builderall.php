<?php

namespace Modules\Integration\Providers;

use Exception;
use GuzzleHttp\Client as HttpClient;

class Builderall extends BaseProvider
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
            $list = [];
            $json = $this->client->request('GET', "https://member.mailingboss.com/integration/index.php/lists/$this->apiKey");
            $request = json_decode($json->getBody());
            if (isset($request->data)) {
                $list = collect($request->data)->map(function ($obj) {
                    return [
                        'id' => $obj->list_uid,
                        'name' => $obj->display_name,
                    ];
                });
            }

            return [
                'lists' => $list,
                'meta' => [
                    'total' => count($list)
                ]
            ];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
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
}
