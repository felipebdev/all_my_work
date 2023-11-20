<?php

namespace Modules\Integration\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Encryption\EncryptException;
use Illuminate\Support\Facades\Crypt;

/**
 * Crypt and decrypt columns table
 */
class Secret implements CastsAttributes
{
    public function get($model, $key, $value, $attributes)
    {
        if (is_null($value)) {
            return null;
        }

        try {
            return Crypt::decrypt($value, true);
        } catch (EncryptException $e) {
            return $value;
        }
    }

    public function set($model, $key, $value, $attributes)
    {
        if (is_null($value)) {
            return null;
        }

        try {
            return Crypt::encrypt($value, true);
        } catch (DecryptException $e) {
            return $value;
        }
    }
}
