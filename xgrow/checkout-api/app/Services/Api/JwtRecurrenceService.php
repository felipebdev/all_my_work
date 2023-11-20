<?php

namespace App\Services\Api;

use Firebase\JWT\JWT;

class JwtRecurrenceService
{

    public string $defaultJwtAlgorithm = 'HS256';

    private $key;

    public function __construct()
    {
        $this->key = config('jwtplatform.jwt_students');
    }

    public function decode(string $jwt): object
    {

        return JWT::decode($jwt, $this->key, [$this->defaultJwtAlgorithm]);
    }

}
