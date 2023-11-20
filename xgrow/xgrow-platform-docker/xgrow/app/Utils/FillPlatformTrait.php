<?php

namespace App\Utils;

use Illuminate\Support\Facades\Auth;

trait FillPlatformTrait
{
    public static function bootFillPlatformTrait()
    {
        static::creating(function ($model) {
            if (in_array('platform_id', $model->fillable)) {
                if (Auth::check()) {
                    $model->platform_id = Auth::user()->platform_id;
                }
            }
        });
    }
}
