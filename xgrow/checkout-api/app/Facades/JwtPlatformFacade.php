<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class JwtPlatformFacade extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'jwtplatform';
    }
}
