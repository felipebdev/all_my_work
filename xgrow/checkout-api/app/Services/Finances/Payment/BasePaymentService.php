<?php


namespace App\Services\Finances\Payment;

use App\Http\Controllers\CouponController;
use App\Plan;
use App\Services\Finances\Objects\OrderBumpsBag;
use App\Services\Finances\Objects\OrderInfo;
use App\Services\Finances\Product\ProductAmountService;
use MundiAPILib\Models\CreateOrderItemRequest;

class BasePaymentService
{
    private ProductAmountService $productAmountService;

    public function __construct()
    {
        $this->productAmountService = new ProductAmountService();
    }

    public function getOrderMetadata($plan, ?OrderInfo $orderInfo = null)
    {
        $metadata = [];
        foreach ($this->getItems($plan) as $cod => $item) {
            if ($item->category == 'product') {
                $metadata['plan_id'] = $item->code;
                $metadata['plan'] = $item->description;
                $metadata['value'] = $item->amount / 100;
            } else { //order_bump
                $metadata['order_bump_plan_id'] = $item->code;
                $metadata['order_bump_plan'] = $item->description;
                $metadata['order_bump_value'] = $item->amount / 100;
            }
        }

        if (!$orderInfo) {
            return $metadata;
        }

        $couponCode = $orderInfo->getCoupom();
        if (strlen($couponCode) > 0) {
            $coupon = CouponController::findCoupon($plan->platform_id, $plan->id, $couponCode);
            if ($coupon) {
                $metadata['cupom'] = $couponCode;
                $metadata['cupom_id'] = $coupon->id;
            }
        }

        $affiliateId = $orderInfo->getAffiliateId();
        if ($affiliateId) {
            $metadata['affiliate_id'] = $affiliateId;
        }

        return $metadata;
    }

    /**
     * Get info from Plan, adding info of OrderBump Plans if supplied
     *
     * @param  \App\Plan  $plan
     * @param  int  $parcelNumber
     * @param  \App\Services\Finances\Objects\OrderBumpsBag|null  $orderBumpsBag
     * @return array
     */
    public function getItems(Plan $plan, $parcelNumber = 1, ?OrderBumpsBag $orderBumpsBag = null)
    {
        $parcelNumber ??= 1;
        $bag = $orderBumpsBag ?? new OrderBumpsBag();

        if ($plan->price > 0) {
            $amount = $this->productAmountService->getPromotionalPlanAmount($plan, $parcelNumber);

            $itemPrice = new CreateOrderItemRequest();
            $itemPrice->description = $plan->name;
            $itemPrice->quantity = 1;
            $itemPrice->amount = "{$amount}";
            $itemPrice->code = $plan->id;
            $itemPrice->category = 'product';
            $items[] = $itemPrice;
        }

        foreach ($bag->getOrderBumpsPlans() as $orderBumpPlan) {
            $amount = $this->productAmountService->getPromotionalPlanAmount($orderBumpPlan, $parcelNumber);

            $itemOrderBump = new CreateOrderItemRequest();
            $itemOrderBump->description = $orderBumpPlan->name;
            $itemOrderBump->quantity = 1;
            $itemOrderBump->amount = "{$amount}";
            $itemOrderBump->code = $orderBumpPlan->id;
            $itemOrderBump->category = 'order_bump';
            $items[] = $itemOrderBump;
        }

        return $items;
    }


}
