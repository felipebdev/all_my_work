<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Recurrence extends Model
{
    const TYPE_SUBSCRIPTION = 'S';

    public function subscriber() {
        return $this->belongsTo('App\Subscriber');
    }

    public function payments() {
        return $this->belongsToMany('App\Payment');
    }

    public function plan() {
        return $this->belongsTo(Plan::class);
    }
}
