<?php

namespace App\Http\Controllers;

use App\Coupon;
use Carbon\Carbon;

class CouponController extends Controller
{
    public static function findCoupon($platform_id, $plan_id, $code) {
        $return = null;
        $coupons = Coupon::where('platform_id', '=', $platform_id)->where('code', '=', $code)->get();
        if ($coupons) {
            foreach ($coupons as $c => $coupon) {
                //Check coupon plan
                foreach ($coupon->plans as $p => $plan) {
                    if ($plan->id == $plan_id) {
                        $return = $coupon;
                    }
                }
            }
        }
        return $return;
    }

    public static function isAvailable(Coupon $coupon, $email = null) {
        $check = false;

        if ( ( is_null($coupon->maturity) || Carbon::now()->lessThan(new Carbon($coupon->maturity ) ) ) && ( is_null($coupon->usage_limit) || $coupon->usage < $coupon->usage_limit ) ) {
            $check = true;
        }

        //Check email exists
        if( $email ) {
            if( $coupon->mailings()->exists() ) {
                return $coupon->mailings()->where('email', '=', $email)->exists();
            }
        }

        return $check;
    }
}
