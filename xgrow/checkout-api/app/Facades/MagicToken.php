<?php

namespace App\Facades;

use App\Services\Api\JwtMagicTokenService;
use Illuminate\Support\Facades\Facade;

/**
 * @mixin JwtMagicTokenService::class
 */
class MagicToken extends Facade
{
    public static function getFacadeAccessor()
    {
        return JwtMagicTokenService::class;
    }
}
