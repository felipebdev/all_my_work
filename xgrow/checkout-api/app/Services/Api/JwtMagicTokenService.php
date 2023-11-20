<?php

namespace App\Services\Api;

use App\Services\Contracts\JwtCheckoutServiceInterface;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Log;

class JwtMagicTokenService implements JwtCheckoutServiceInterface
{

    public $defaultJwtAlgorithm = 'HS256';

    private $secret = null;

    public function __construct()
    {
        $this->secret = config('jwtplatform.jwt_magic');
        if (!$this->secret) {
            Log::alert('JWT_MAGIC_SECRET env variable is not set, using unsafe default secret');
            $this->secret = 'secret';
        }
    }

    public function generate(string $platformId, string $subscriberId, ?array $additionalPayloadData = []): string
    {
        $minimumPayload = [
            'iat' => Carbon::now()->timestamp,
            'platform_id' => $platformId,
            'subscriber_id' => $subscriberId,
        ];

        $payload = array_merge($minimumPayload, $additionalPayloadData ?? []);

        $secret = $this->secret;

        $jwt = JWT::encode($payload, $secret, $this->defaultJwtAlgorithm);

        return $jwt;
    }

    public function decode(string $jwt)
    {
        $payload = JWT::decode($jwt, $this->secret, [$this->defaultJwtAlgorithm]);

        return $payload;
    }

}
