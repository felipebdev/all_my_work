<?php

namespace App\Services\Api;

use App\Services\Contracts\JwtCheckoutServiceInterface;
use Firebase\JWT\JWT;

class JwtCheckoutService implements JwtCheckoutServiceInterface
{

    public $defaultJwtAlgorithm = 'HS256';

    private $key;

    public function __construct()
    {
        $this->key = config('jwtplatform.jwt_checkout');
    }

    public function decode(string $jwt)
    {
        $payload = JWT::decode($jwt, $this->key, [$this->defaultJwtAlgorithm]);
        return $payload;
    }

}
