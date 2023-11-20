<?php

namespace App\Services\Objects;

class SaleReportFilter
{
    public $search = null;
    public $products = null;
    public $plans = null;
    public $paymentMethod = null;
    public $paymentStatus = null;
    public $paymentPeriod = null;
    public $subscriptionStatus = null;
    public $accessionPeriod;
    public $cancelPeriod;
    public $lastPaymentPeriod;
    public $paymentType;
    public $onlyPaymentWithMultipleMeans;
    public $onlyPaymentWithCoupon;

    public function __construct(
        ?string $search = null,
        ?array $products = null,
        ?array $plans = null,
        ?array $paymentMethod = null,
        ?array $paymentStatus = null,
        ?array $subscriptionStatus = null,
        ?PeriodFilter $paymentPeriod = null,
        ?PeriodFilter $accessionPeriod = null,
        ?PeriodFilter $cancelPeriod = null,
        ?PeriodFilter $lastPaymentPeriod = null,
        ?array $paymentType = null,
        ?bool $onlyPaymentWithMultipleMeans = false,
        ?bool $onlyPaymentWithCoupon = false
    ) {
        $this->search = $search;
        $this->products = $products;
        $this->plans = $plans;
        $this->paymentMethod = $paymentMethod;
        $this->paymentStatus = $paymentStatus;
        $this->subscriptionStatus = $subscriptionStatus;
        $this->paymentPeriod = $paymentPeriod;
        $this->accessionPeriod = $accessionPeriod;
        $this->cancelPeriod = $cancelPeriod;
        $this->lastPaymentPeriod = $lastPaymentPeriod;
        $this->paymentType = $paymentType;
        $this->onlyPaymentWithMultipleMeans = $onlyPaymentWithMultipleMeans;
        $this->onlyPaymentWithCoupon = $onlyPaymentWithCoupon;
    }

    /**
     * @param  string|null  $search
     * @return SaleReportFilter
     */
    public function setSearch(?string $search): SaleReportFilter
    {
        $this->search = $search;
        return $this;
    }

    /**
     * @param  array|null  $products
     * @return SaleReportFilter
     */
    public function setProducts(?array $products): SaleReportFilter
    {
        $this->products = $products;
        return $this;
    }

    /**
     * @param  array|null  $plans
     * @return SaleReportFilter
     */
    public function setPlans(?array $plans): SaleReportFilter
    {
        $this->plans = $plans;
        return $this;
    }

    /**
     * @param  array|null  $paymentMethod
     * @return SaleReportFilter
     */
    public function setPaymentMethod(?array $paymentMethod): SaleReportFilter
    {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }

    /**
     * @param  array|null  $paymentStatus
     * @return SaleReportFilter
     */
    public function setPaymentStatus(?array $paymentStatus): SaleReportFilter
    {
        $this->paymentStatus = $paymentStatus;
        return $this;
    }

    /**
     * @param  \App\Services\Objects\PeriodFilter|null  $paymentPeriod
     * @return SaleReportFilter
     */
    public function setPaymentPeriod(?PeriodFilter $paymentPeriod): SaleReportFilter
    {
        $this->paymentPeriod = $paymentPeriod;
        return $this;
    }

    /**
     * @param  array|null  $subscriptionStatus
     * @return SaleReportFilter
     */
    public function setSubscriptionStatus(?array $subscriptionStatus): SaleReportFilter
    {
        $this->subscriptionStatus = $subscriptionStatus;
        return $this;
    }

    /**
     * @param  \App\Services\Objects\PeriodFilter|null  $accessionPeriod
     * @return SaleReportFilter
     */
    public function setAccessionPeriod(?PeriodFilter $accessionPeriod): SaleReportFilter
    {
        $this->accessionPeriod = $accessionPeriod;
        return $this;
    }

    /**
     * @param  \App\Services\Objects\PeriodFilter|null  $cancelPeriod
     * @return SaleReportFilter
     */
    public function setCancelPeriod(?PeriodFilter $cancelPeriod): SaleReportFilter
    {
        $this->cancelPeriod = $cancelPeriod;
        return $this;
    }

    /**
     * @param  \App\Services\Objects\PeriodFilter|null  $lastPaymentPeriod
     * @return SaleReportFilter
     */
    public function setLastPaymentPeriod(?PeriodFilter $lastPaymentPeriod): SaleReportFilter
    {
        $this->lastPaymentPeriod = $lastPaymentPeriod;
        return $this;
    }

    /**
     * @param  array|null  $paymentType
     * @return SaleReportFilter
     */
    public function setPaymentType(?array $paymentType): SaleReportFilter
    {
        $this->paymentType = $paymentType;
        return $this;
    }

    /**
     * @param  bool|null  $onlyPaymentWithMultipleMeans
     * @return SaleReportFilter
     */
    public function setOnlyPaymentWithMultipleMeans(?bool $onlyPaymentWithMultipleMeans): SaleReportFilter
    {
        $this->onlyPaymentWithMultipleMeans = $onlyPaymentWithMultipleMeans;
        return $this;
    }

    /**
     * @param  bool|null  $onlyPaymentWithCoupon
     * @return SaleReportFilter
     */
    public function setOnlyPaymentWithCoupon(?bool $onlyPaymentWithCoupon): SaleReportFilter
    {
        $this->onlyPaymentWithCoupon = $onlyPaymentWithCoupon;
        return $this;
    }
}
