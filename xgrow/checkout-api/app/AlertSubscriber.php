<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AlertSubscriber extends Model
{
    protected $table="alert_subscriber";
    protected $fillable = ['subscriber_id', 'alert_id'];
}
