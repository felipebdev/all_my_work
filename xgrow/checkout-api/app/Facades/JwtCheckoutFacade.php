<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class JwtCheckoutFacade extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'jwtcheckout';
    }
}
