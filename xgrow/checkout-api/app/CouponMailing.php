<?php

namespace App;

use App\Plan;
use Illuminate\Database\Eloquent\Model;

class CouponMailing extends Model
{
    protected $fillable = [
        'name', 'email', 'notes', 'isSent', 'coupon_id'
    ];

    public function plan() {
        return $this->belongsTo(Plan::class);
    }

}
