<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Safe extends Model
{

    protected $fillable = [ 'card_id', 'number_token', 'platform_id' ];

    public function integration()
    {
        return $this->morphOne(IntegrationType::class, 'integratable');
    }
}
