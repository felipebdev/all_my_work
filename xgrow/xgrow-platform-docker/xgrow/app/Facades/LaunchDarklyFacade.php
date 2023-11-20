<?php


namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class LaunchDarklyFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'featureflag';
    }
}
