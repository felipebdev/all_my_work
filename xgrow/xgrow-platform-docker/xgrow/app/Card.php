<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{

    public function integratable()
    {
        return $this->morphMany(IntegrationType::class, 'integratable');
    }

//    public function integration()
//    {
//        return $this->morphMany(IntegrationType::class, 'integratable');
//    }
}
