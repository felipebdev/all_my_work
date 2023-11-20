<?php

namespace App\Services\Integrations;

use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;

/**
 * Class FacebookPixelServiceFake
 *
 * @package App\Services\Integrations
 */
class FakeFacebookPixelService extends FacebookPixelService
{

    /**
     * Only log
     *
     * @param  string  $id
     * @param  string  $token
     * @param  array  $data
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function sendSingleRawData(array $data): ResponseInterface
    {
        Log::debug('Facebook Pixel fake data sending', [
            'data' => $data
        ]);
        return new Response();
    }
}
