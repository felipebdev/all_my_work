<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PushNotification extends Model
{
    const TYPE_MOBILE = 'mobile';
    const TYPE_DESKTOP = 'desktop';

    use SoftDeletes;

    protected $dates = [
        'run_at',
    ];

    protected $fillable = [
        'title',
        'text',
        'run_at',
        'platform_id',
        'is_sent',
        'user_id',
        'type',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

}
