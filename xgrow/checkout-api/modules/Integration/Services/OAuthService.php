<?php

namespace Modules\Integration\Services;

use Illuminate\Support\Str;
use Modules\Integration\Contracts\IAppIntegrationService;
use Modules\Integration\Contracts\IOAuthable;
use Modules\Integration\Contracts\IOAuthService;
use Modules\Integration\Enums\CodeEnum;

class OAuthService implements IOAuthService
{
    /**
     * @var Modules\Integration\Contracts\IAppIntegrationService
     */
    private $appIntegrationService;

    public function __construct(IAppIntegrationService $appIntegrationService)
    {
        $this->appIntegrationService = $appIntegrationService;
    }

    public function save(
        IOAuthable $oAuth,
        string $platformId,
        string $code
    ): void {
        $token = $oAuth->accessToken($code);
        $provider = $oAuth->provider();
        $request = [
            'platform_id' => $platformId,
            'description' => Str::ucfirst($provider),
            'is_active' => true,
            'code' => CodeEnum::getValue(strtoupper($provider)),
            'type' => $provider,
            'api_key' => $token->getAccessToken(),
            'api_secret' => $token->getRefreshToken(),
            'metadata' => [
                'expires_in' => $token->getExpiresIn()
            ]
        ];
        $this->appIntegrationService->store($request);
    }
}
