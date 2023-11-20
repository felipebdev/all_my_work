<?php

namespace App\Services\Mundipagg\Calculator\Strategies;

use App\Services\Mundipagg\Calculator\Contracts\CalculatorStrategy;
use App\Services\Mundipagg\Calculator\Objects\AmountResult;

class SimpleCalculator implements CalculatorStrategy
{

    public function calculate(
        int $price,
        int $coupon,
        float $percentSplit,
        int $clientTaxTransaction,
        int $installmentNumber = 1,
        int $totalInstallments = 1
    ): AmountResult {
        $value = $price;

        $valueWithDiscounts = $price - $coupon;

        $baseCustomerValue = (int) round($percentSplit / 100 * $valueWithDiscounts);

        $remainder = $valueWithDiscounts - $baseCustomerValue;

        $customerValue = $baseCustomerValue - $clientTaxTransaction;

        $tax = $remainder + $clientTaxTransaction;

        return AmountResult::create($value, $valueWithDiscounts, $customerValue, $tax, $coupon);
    }

}
