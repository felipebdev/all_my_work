<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class AccessLogFacade extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'accesslog';
    }
}
