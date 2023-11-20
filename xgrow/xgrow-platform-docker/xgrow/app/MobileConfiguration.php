<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MobileConfiguration extends Model
{
    protected $fillable = [
        'platforms_users_id',
        'notifications',
        'notifications_sells',
        'notifications_sells_product_name',
    ];

    protected $casts = [
        'notifications' => 'boolean',
        'notifications_sells' => 'boolean',
        'notifications_sells_product_name' => 'boolean',
    ];
}
