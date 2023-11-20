<?php

namespace App\Services\Finances\Objects;

use App\Plan;
use App\Services\Finances\Product\ProductAmountService;

/**
 * Class OrderInfoPriceTag helps creation of PriceTag
 *
 * @package App\Services\Finances\Objects
 */
class OrderInfoPriceTag
{
    private OrderInfo $orderInfo;

    private ProductAmountService $productAmountService;

    public function __construct(OrderInfo $orderInfo)
    {
        $this->orderInfo = $orderInfo;
        $this->productAmountService = new ProductAmountService();
    }

    public function planPriceTag(): PriceTag
    {
        $planAmountWithCoupom = $this->productAmountService->getTotalMainPlanWithDiscount($this->orderInfo);

        return PriceTag::fromInt($this->orderInfo->getPlanId(), $planAmountWithCoupom);
    }

    /**
     * @return \App\Services\Finances\Objects\PriceTag[]
     */
    public function orderBumpPriceTags(): array
    {
        $orderBumps = $this->orderInfo->getOrderBumpsBag()->getOrderBumpsPlans();

        return array_map(function (Plan $orderBump) {
            $amount = $this->productAmountService->getPromotionalPlanAmount($orderBump);

            return PriceTag::fromInt($orderBump->id, $amount);
        }, $orderBumps);
    }


}
