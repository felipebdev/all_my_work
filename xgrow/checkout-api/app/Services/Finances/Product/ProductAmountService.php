<?php

namespace App\Services\Finances\Product;

use App\Coupon;
use App\Plan;
use App\Services\Finances\Objects\OrderInfo;

/**
 * Class ProductAmountService helps calculation of product amount from OrderInfo
 *
 * Methods return int (BRL "centavos")
 */
class ProductAmountService
{

    /**
     * Get total amount of main plan from Order (including discounts like promotional price, coupon, etc)
     *
     * @param  \App\Services\Finances\Objects\OrderInfo  $orderInfo
     * @return int Total of main plan with discount in "centavos"
     */
    public function getTotalMainPlanWithDiscount(OrderInfo $orderInfo): int
    {
        $plan = $orderInfo->finder->rememberPlan();

        $amount = $this->getPromotionalPlanAmount($plan);

        $discount = $this->getCouponDiscountAmount($amount, $orderInfo->getCoupom());

        $totalMainPlan = $amount - $discount;

        return $totalMainPlan;
    }

    /**
     * Get discount of coupon
     *
     * @param  int  $planAmount  Plan amount (in "centavos")
     * @param  string|null  $couponCode
     * @return int Discount in "centavos"
     */
    private function getCouponDiscountAmount(int $planAmount, ?string $couponCode = ''): int
    {
        if (!$couponCode) {
            return 0;
        }

        /** @var Coupon $coupon */
        $coupon = Coupon::where('code', $couponCode)->first();

        if (!$coupon) {
            return 0;
        }

        $couponAmount = $coupon->getDiscountValue($planAmount);

        return (int) round($couponAmount, 0, PHP_ROUND_HALF_DOWN);
    }

    /**
     * Apply promotional price (if any) on plan
     *
     * @param  \App\Plan  $plan
     * @param  int  $parcelNumber
     * @return int Amount in BRL "centavos"
     */
    public function getPromotionalPlanAmount(Plan $plan, $parcelNumber = 1): int
    {
        $price = $plan->getPrice($parcelNumber); //original price in BRL

        $amount = (int) round($price * 100, 0, PHP_ROUND_HALF_DOWN);

        return $amount;
    }

}
