<?php

namespace App\Services\Contracts;

interface JwtPlatformServiceInterface
{
    public function generateToken(
        $platformId,
        string $email,
        string $documentNumber,
        int $expirationInMinutes = 30
    ): string;

    public function decode(string $jwt);
}
