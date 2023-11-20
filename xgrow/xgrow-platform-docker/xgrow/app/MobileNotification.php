<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MobileNotification extends Model
{
    protected $casts = [
        'id' => 'string'
    ];

    protected $fillable = [
        'platform_id',
        'platforms_users_id',
        'title',
        'body',
        'read',
    ];
}
