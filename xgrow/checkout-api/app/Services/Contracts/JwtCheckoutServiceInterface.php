<?php

namespace App\Services\Contracts;

/**
 * Interface JwtCheckoutServiceInterface
 *
 * Validates JWT from checkout
 *
 * @package App\Services\Contracts
 */
interface JwtCheckoutServiceInterface
{
    public function decode(string $jwt);
}
