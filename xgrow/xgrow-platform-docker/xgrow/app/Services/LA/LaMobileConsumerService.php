<?php

namespace App\Services\LA;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class LaMobileConsumerService
{
    /** Verify if token exists
     * @param $token
     * @return object
     * @throws Exception
     */
    public function hasToken($token): object
    {

        if (!$token) {
            throw new Exception("Valid Token is required.");
        }

        $key = config('jwtplatform.jwt_mobile_api_key');

        $tokenDecoded = JWT::decode($token, new Key($key, 'HS256'));

        if (!$tokenDecoded) {
            throw new Exception("Token invalid.");
        }

//        if (!isset($tokenDecoded->platformId)) {
//            throw new Exception("PlatformId not informed.");
//        };

        return $tokenDecoded;
    }
}