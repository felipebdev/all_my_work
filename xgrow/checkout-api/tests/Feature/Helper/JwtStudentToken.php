<?php

namespace Tests\Feature\Helper;

use Carbon\Carbon;
use Firebase\JWT\JWT;

class JwtStudentToken
{

    public static $secret = null;

    public static $expirationInMinutes = 5000;

    public const ALGORITHM = 'HS256';

    public static function generateToken(
        string $email,
        array $subscribersIds,
        array $productsIds,
        ?array $additionalPayloadData = []
    ): string {
        $minimumPayload = [
            'exp' => Carbon::now()->addMinutes(static::$expirationInMinutes)->timestamp,
            'email' => $email,
            'subscribers_ids' => $subscribersIds,
            'products_ids' => $productsIds,
        ];

        $payload = array_merge($minimumPayload, $additionalPayloadData ?? []);

        $secret = static::$secret ?? env('JWT_STUDENTS_SECRET') ?? 'secret';

        $jwt = JWT::encode($payload, $secret, self::ALGORITHM);

        return $jwt;
    }

}
