<?php

namespace Tests\Feature\Helper;

use Carbon\Carbon;
use Firebase\JWT\JWT;

class JwtWebToken
{

    public static $expirationInMinutes = 60;

    public const ALGORITHM = 'HS256';

    public static function generateToken(string $platformId, string $userId, ?array $additionalPayloadData = []): string
    {
        $minimumPayload = [
            'exp' => Carbon::now()->addMinutes(static::$expirationInMinutes)->timestamp,
            'platform_id' => $platformId,
            'user_id' => $userId,
        ];

        $payload = array_merge($minimumPayload, $additionalPayloadData ?? []);

        $secret = config('jwtplatform.jwt_web');

        if (!$secret) {
            throw new \Exception('JWT_WEB env missing');
        }

        $jwt = JWT::encode($payload, $secret, self::ALGORITHM);

        return $jwt;
    }

    /**
     * Token WITHOUT platformId
     *
     * @param  string  $userId
     * @param  array|null  $additionalPayloadData
     * @return string
     */
    public static function generateUserToken(string $userId, ?array $additionalPayloadData = []): string
    {
        $minimumPayload = [
            'exp' => Carbon::now()->addMinutes(static::$expirationInMinutes)->timestamp,
            'user_id' => $userId,
        ];

        $payload = array_merge($minimumPayload, $additionalPayloadData ?? []);

        $secret = config('jwtplatform.jwt_web');

        if (!$secret) {
            throw new \Exception('JWT_WEB env missing');
        }

        $jwt = JWT::encode($payload, $secret, self::ALGORITHM);

        return $jwt;
    }

    public static function generateDocToken()
    {
        $minimumPayload = [
            'exp' => 1234567890,
            'platform_id' => '00000000-0000-0000-0000-000000000000',
            'user_id' => '123',
            'recipient_id' => 'or_abcdefg'
        ];

        $payload = array_merge($minimumPayload, $additionalPayloadData ?? []);
        $jwt = JWT::encode($payload, 'secret', self::ALGORITHM);

        return $jwt;
    }

}
