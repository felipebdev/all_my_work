<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = ['code', 'status', 'amount', 'paid_date', 'paid_time', 'platform_id', 'order_id'];


}
