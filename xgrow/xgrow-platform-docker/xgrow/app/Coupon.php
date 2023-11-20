<?php

namespace App;

use App\Plan;
use App\CouponMailing;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use SoftDeletes;

    const TYPE_VALUE = 'V';
    const TYPE_PERCENT = 'P';

    protected $fillable = [
        'code', 'description', 'maturity', 'occurrences', 'value', 'value_type',
        'usage_limit', 'plan_id', 'platform_id'
    ];

    public function plans() {
        return $this->belongsToMany(Plan::class);
    }

    public function mailings() {
        return $this->hasMany(CouponMailing::class);
    }

    public function getDiscountValue($itemValue) {
        if( $this->value_type == 'P' ) { //Percent
            $discount = round(( $itemValue * ($this->value/100)),2);
        } else { //Value
            $discount = $this->value;
        }
        return $discount;
    }
}
