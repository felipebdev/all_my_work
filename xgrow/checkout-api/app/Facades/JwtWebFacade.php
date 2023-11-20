<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static setPayload(\stdClass|null $payload)
 * @method static \stdClass getPayload()
 *
 * @see \App\Helpers\JwtWebHelper
 */
class JwtWebFacade extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'jwtweb';
    }
}
