<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CacheEntry extends Model
{
    protected $fillable = [
        'name',
        'description',
        'default_value',
    ];
}
