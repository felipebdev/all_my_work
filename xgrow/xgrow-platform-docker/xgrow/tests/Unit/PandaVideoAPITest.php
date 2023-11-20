<?php

namespace Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Tests\TestCase;

class PandaVideoAPITest extends TestCase
{
    public function test_get_videos_200()
    {
        $uri = config('app.panda_video_url');
        $headers = ['headers' => ['Accept' => 'application/json', 'Authorization' => env('PANDA_API')]];
        $client = new Client(['base_uri' => $uri]);
        $res = $client->get('videos', $headers);
        $this->assertEquals(200, $res->getStatusCode());
    }

    public function test_get_videos_unauthorized_401()
    {
        $uri = config('app.panda_video_url');
        $headers = ['headers' => ['Accept' => 'application/json']];
        $client = new Client(['base_uri' => $uri]);
        try {
            $client->get('videos', $headers);
        } catch (GuzzleException $e) {
            $this->assertEquals(401, $e->getCode());
        }
    }

    public function test_get_videos_url_invalid_403()
    {
        $uri = config('app.panda_video_url');
        $headers = ['headers' => ['Accept' => 'application/json', 'Authorization' => env('PANDA_API')]];
        $client = new Client(['base_uri' => $uri]);
        try {
            $client->get('videosInvalid', $headers);
        } catch (GuzzleException $e) {
            $this->assertEquals(403, $e->getCode());
        }
    }

    public function test_get_video_invalid_uuid_500()
    {
        $uri = config('app.panda_video_url');
        $headers = ['headers' => ['Accept' => 'application/json', 'Authorization' => env('PANDA_API')]];
        $client = new Client(['base_uri' => $uri]);
        try {
            $client->get('videos/1', $headers);
        } catch (GuzzleException $e) {
            $this->assertEquals(500, $e->getCode());
        }
    }
}
