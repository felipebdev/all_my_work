<?php

namespace Modules\Integration\Casts;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Encryption\EncryptException;
use Illuminate\Support\Facades\Crypt;
use Vkovic\LaravelCustomCasts\CustomCastBase;

/**
 * Crypt and decrypt columns table
 */
class Secret extends CustomCastBase
{
    public function setAttribute($value)
    {
        try {
            return Crypt::encrypt($value, true);
        } catch (EncryptException $e) {
            return $value;
        }
    }

    public function castAttribute($value)
    {
        try {
            return Crypt::decrypt($value , true);
        } catch (DecryptException $e) {
            return $value;
        }
    }
}
