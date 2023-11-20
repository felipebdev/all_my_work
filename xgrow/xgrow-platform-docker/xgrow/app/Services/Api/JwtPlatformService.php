<?php

namespace App\Services\Api;

use App\Services\Contracts\JwtPlatformServiceInterface;
use Carbon\Carbon;
use Firebase\JWT\JWT;

class JwtPlatformService implements JwtPlatformServiceInterface
{

    public $defaultJwtAlgorithm = 'HS256';

    private $key;

    public function __construct()
    {
        $this->key = config('jwtplatform.jwt_platform');
    }

    public function generateToken(
        $platformId,
        string $email,
        string $documentNumber,
        int $expirantionInMinutes = 30
    ): string {
        $token = [
            'exp' => Carbon::now()->addMinutes($expirantionInMinutes)->timestamp,
            'platform_id' => $platformId,
            'email' => $email,
            'document_number' => $documentNumber,
        ];

        $jwt = JWT::encode($token, $this->key, $this->defaultJwtAlgorithm);

        return $jwt;
    }

    public function decode(string $jwt)
    {
        $payload = JWT::decode($jwt, $this->key, [$this->defaultJwtAlgorithm]);

        return $payload;
    }

}
