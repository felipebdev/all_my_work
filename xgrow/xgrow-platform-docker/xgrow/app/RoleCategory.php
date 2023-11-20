<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoleCategory extends Model
{
    public function roles(){
        return $this->hasMany(Role::class);
    }
}
