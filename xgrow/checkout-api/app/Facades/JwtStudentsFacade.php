<?php

namespace App\Facades;

use App\Helpers\JwtStudentsHelper;
use Illuminate\Support\Facades\Facade;

/**
 * @method static setPayload(\stdClass|null $payload)
 * @method static \stdClass getPayload()
 * @method static string|null getEmail()
 * @method static array getSubscribersIds()
 * @method static array getProductsIds()
 *
 * @see \App\Helpers\JwtStudentsHelper
 */

class JwtStudentsFacade extends Facade
{
    public static function getFacadeAccessor()
    {
        return JwtStudentsHelper::class;
    }
}
