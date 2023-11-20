<?php

namespace Modules\Integration\Providers;

use Infusionsoft\Infusionsoft;
use Infusionsoft\Token;
use Modules\Integration\Contracts\IOAuthable;
use Modules\Integration\Enums\TypeEnum;
use Modules\Integration\Models\OAuthToken;

class Infusion extends BaseProvider implements IOAuthable
{
    private $client;

    public function __construct()
    {
        $this->client = new Infusionsoft([
            'clientId'     => config('apps.services.infusion.app_key'),
            'clientSecret' => config('apps.services.infusion.app_secret'),
            'redirectUri'  => route('apps.integrations.oauth.callback')
        ]);
    }

    public function provider(): string
    {
        return TypeEnum::INFUSION;
    }

    public function accessToken(string $code): OAuthToken
    {
        $this->client->requestAccessToken($code);
        $token = $this->client->getToken();
        
        return new OAuthToken(
            $token->getAccessToken(),
            $token->getRefreshToken(),
            $token->getEndOfLife()
        );
    }

    public function refreshToken(
        string $accessToken,
        string $refreshToken,
        int $expiresIn
    ): OAuthToken {   
        $this->client->setToken(new Token([
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_in' => $expiresIn
        ]));

        $this->client->refreshAccessToken();
        $token = $this->client->getToken();

        return new OAuthToken(
            $token->getAccessToken(),
            $token->getRefreshToken(),
            $token->getEndOfLife()
        );
    }

    public function tags()
    {
        $this->client->setToken(new Token([
            'access_token' => $this->apiKey,
            'refresh_token' => $this->apiSecret,
            'expires_in' => $this->metadata['expires_in']
        ]));

        $tags = $this->client->tags()->all();
        return $tags;
    }
}
