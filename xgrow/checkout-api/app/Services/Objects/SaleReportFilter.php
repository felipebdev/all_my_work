<?php

namespace App\Services\Objects;

class SaleReportFilter
{
    public $search = null;
    public $products = null;
    public $plans = null;
    public $paymentType = null;
    public $paymentStatus = null;
    public $paymentPeriod = null;
    public $subscriptionStatus = null;
    public $accessionPeriod;
    public $cancelPeriod;
    public $lastPaymentPeriod;

    public function __construct(
        string $search = null,
        array $products = null,
        array $plans = null,
        array $paymentType = null,
        array $paymentStatus = null,
        array $subscriptionStatus = null,
        ?PeriodFilter $paymentPeriod = null,
        ?PeriodFilter $accessionPeriod = null,
        ?PeriodFilter $cancelPeriod = null,
        ?PeriodFilter $lastPaymentPeriod = null
    ) {
        $this->search = $search;
        $this->products = $products;
        $this->plans = $plans;
        $this->paymentType = $paymentType;
        $this->paymentStatus = $paymentStatus;
        $this->subscriptionStatus = $subscriptionStatus;
        $this->paymentPeriod = $paymentPeriod;
        $this->accessionPeriod = $accessionPeriod;
        $this->cancelPeriod = $cancelPeriod;
        $this->lastPaymentPeriod = $lastPaymentPeriod;
    }
}
