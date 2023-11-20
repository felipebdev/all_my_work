<?php

namespace App\Services\Finances\Product;

use App\Plan;
use App\Services\Finances\Objects\Constants;
use Carbon\Carbon;

class ProductPaymentService
{

    public static function pixExpiresAt(Plan $plan): Carbon
    {
        $seconds = self::pixExpirationInSeconds($plan);
        return Carbon::now()->addSeconds($seconds);
    }

    public static function pixExpirationInSeconds(Plan $plan): int
    {
        return Constants::PIX_EXPIRATION_SECONDS;
    }

    /**
     * Due date on checkout
     *
     * Due date = Checkout date + payout limit
     *
     * @param  \App\Plan  $plan
     * @return \Carbon\Carbon
     */
    public static function boletoCheckoutDueAt(Plan $plan): Carbon
    {
        $days = self::boletoPayoutLimitInDays($plan);

        return Carbon::now()->addWeekdays($days);
    }

    /**
     * Expiration date on checkout
     *
     * Expiration = Checkout date + payout limit + compensation time
     *
     * @param  \App\Plan  $plan
     * @return \Carbon\Carbon
     */
    public static function boletoCheckoutExpirationDate(Plan $plan): Carbon
    {
        $days = self::boletoPayoutLimitInDays($plan);

        return Carbon::now()->addWeekdays($days)->addWeekdays(Constants::BOLETO_EXPIRATION_WEEKDAYS);
    }

    /**
     * Expiration date on renew subscription
     *
     * Expiration = Due date + compensation time
     *
     * @param  \Carbon\Carbon  $dueAt
     * @return \Carbon\Carbon
     */
    public static function boletoRenewExpirationDate(Carbon $dueAt): Carbon
    {
        return $dueAt->clone()->addWeekdays(Constants::BOLETO_EXPIRATION_WEEKDAYS);
    }

    public static function boletoPayoutLimitInDays(Plan $plan): int
    {
        return $plan->boleto_payout_limit ?? Constants::BOLETO_PAYOUT_LIMIT_DEFAULT;
    }
}
