<?php

namespace App\Services\Finances\Objects;

use App\Plan;
use App\Platform;
use App\Producer;

/**
 * Class OrderInfoModelFinder helps retrieve IMMUTABLE models (load once and keep in memory).
 *
 * An IMMUTABLE model is a model that can't be modified in the request lifecycle, eg:
 *
 * Once the request started, any external change on Plan's database record SHOULD NOT reflect into the order process.
 *
 * @package App\Services\Finances\Objects
 */
class OrderInfoModelFinder
{
    private ?Platform $platform = null;
    private ?Plan $plan = null;
    private ?Producer $affiliate = null;

    private OrderInfo $orderInfo;

    public function __construct(OrderInfo $orderInfo)
    {
        $this->orderInfo = $orderInfo;
    }

    /**
     * Get platform Model
     *
     * @return \App\Platform
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function rememberPlatform(): Platform
    {
        return $this->platform ??= Platform::find($this->orderInfo->getPlatformId());
    }

    /**
     * Get plan model
     *
     * @return \App\Plan
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function rememberPlan(): Plan
    {
        return $this->plan ??= Plan::findOrFail($this->orderInfo->getPlanId());
    }

    public function rememberAffiliate(): ?Producer
    {
        return $this->affiliate ??= Producer::find($this->orderInfo->getAffiliateId());
    }

}
