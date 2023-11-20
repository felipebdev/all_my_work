<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{

    use hasFactory;

    protected $fillable = ['name', 'platform_id'];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'permission_role', 'permission_id', 'role_id');
    }

    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }

    public function platformusers(): BelongsToMany
    {
        return $this->belongsToMany(PlatformUser::class, 'permission_platformuser', 'permission_id', 'platformuser_id');
    }
}
