<?php

namespace App;

use App\PlatformUser;
use App\Role;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = ['name', 'platform_id'];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'permission_role', 'permission_id', 'role_id');
    }

    public function platformusers()
    {
        return $this->belongsToMany(PlatformUser::class,
                                    'platform_user',
                                    'permission_id',
                                    'platforms_users_id');
    }


}
