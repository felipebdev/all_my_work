<?php

namespace App\Services\Finances\PaymentChange;

use App\Services\Contracts\JwtPlatformServiceInterface;

class ChangeCardUrlService
{
    private JwtPlatformServiceInterface $jwtPlatformService;


    public function __construct(JwtPlatformServiceInterface $jwtPlatformService)
    {
        $this->jwtPlatformService = $jwtPlatformService;
    }

    public function generateUrlWithToken(string $platformId, string $email, ?string $documentNumber = null): string
    {
        $token = $this->jwtPlatformService->generateToken($platformId, $email, $documentNumber ?? '');

        $query = http_build_query([
            'token' => $token,
        ]);

        $urlWithToken = "{$this->baseUrl()}/{$platformId}/dashboard?{$query}";

        return $urlWithToken;
    }

    private function baseUrl()
    {
        return env('APP_URL_SETTINGS', 'https://settings.xgrow.com');
    }

}
