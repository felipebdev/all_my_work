<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IntegrationType extends Model
{
    protected $fillable = ['integration_id', 'integration_type_id'];

    public function integratable()
    {
        return $this->morphTo();
    }

    public function integration()
    {
        return $this->hasMany(Integration::class, 'id', 'integration_id');
    }


}
