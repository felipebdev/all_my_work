<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlatformUserAccess extends Model
{
    protected $table = 'platform_user';

    public $timestamps = false;

    protected $fillable = ['platform_id', 'platforms_users_id', 'type_access', 'permission_id'];

    public function platform(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Platform::class, 'platform_id', 'id');
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PlatformUser::class, 'platforms_users_id', 'id');
    }
}
