<?php

namespace App\Services\PlatformAPI;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;


class PlatformAPIService
{
    private Client $platformService;

    public function __construct(PlatformAPIBaseService $baseService)
    {
        $this->platformService = $baseService->connectionConfig();
    }

    /**
     * Send subscriber id to send email on platform
     * @param String $subscriberId
     * @return string
     * @throws GuzzleException
     */
    public function resendData(string $subscriberId): string
    {
        $url = Config::get('jwt.platform.url');
        $response = $this->platformService->post('backoffice/subscribers/resend-data', ['json' => ['subscriberId' => $subscriberId]]);

        Log::info('Resend Subscriber Data to PlatformAPI', [
            'method' => 'POST',
            'uri' => $url . "subscribers/resend-data",
            'code' => $response->getStatusCode()
        ]);

        return $response->getBody()->getContents();
    }
}
