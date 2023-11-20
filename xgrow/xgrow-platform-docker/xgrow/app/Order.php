<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['order_integration'];

    public function integratable()
    {
        return $this->morphMany(IntegrationType::class, 'integratable');
    }


    public function orderable()
    {
        return $this->morphTo();
    }
}
