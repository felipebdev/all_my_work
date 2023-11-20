<?php

namespace Modules\Integration\Contracts;

use Modules\Integration\Models\OAuthToken;

interface IOAuthable
{
    public function provider(): string;
    public function accessToken(string $code): OAuthToken;
    public function refreshToken(
        string $accessToken,
        string $refreshToken,
        int $expiresIn
    ): OAuthToken;
}
