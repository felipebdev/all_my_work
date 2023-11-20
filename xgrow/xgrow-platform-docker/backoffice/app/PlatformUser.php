<?php

namespace App;

use App\Http\Traits\ElasticsearchTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlatformUser extends Model
{
    use SoftDeletes, ElasticsearchTrait;

    protected $table = 'platforms_users';

    protected $fillable = ['name', 'email', 'password', 'active'];

    protected $hidden = ['password', 'remember_token', 'platform_id', 'permission_id'];

    public function getStatusAttribute(){
    	return $this->active ? 'Ativo' : 'Inativo';
    }

    public function platforms() {
        return $this->belongsToMany(
            Platform::class,
            'platform_user',
            'platforms_users_id',
            'platform_id'
        );
    }

}
