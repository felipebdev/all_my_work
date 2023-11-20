<?php

namespace App\Services\Mundipagg\Calculator\Strategies;

use App\Services\Finances\Objects\Coin;
use App\Services\Mundipagg\Calculator\Contracts\CalculatorStrategy;
use App\Services\Mundipagg\Calculator\Objects\AmountResult;

class UnlimitedCalculator implements CalculatorStrategy
{
    public function calculate(
        int $price,
        int $coupon,
        float $percentSplit,
        int $clientTaxTransaction,
        int $installmentNumber = 1,
        int $totalInstallments = 1
    ): AmountResult {
        $valueWithDiscounts = $price - $coupon;

        $baseCustomerValue = (int) round($percentSplit / 100 * $valueWithDiscounts);

        $remainder = $valueWithDiscounts - $baseCustomerValue;

        $tax = Coin::fromInt($remainder + $totalInstallments * $clientTaxTransaction)
            ->installmentNumber($installmentNumber, $totalInstallments)
            ->getAmount();

        $installmentAmount = Coin::fromInt($baseCustomerValue)
            ->installmentNumber($installmentNumber,$totalInstallments)
            ->getAmount();

        $customerValue = (int) ($installmentAmount - $clientTaxTransaction);

        // adjust values per installment
        $value = (int) round($price / $totalInstallments);

        $valueWithDiscounts = (int) round($valueWithDiscounts / $totalInstallments);

        $coupon = (int) round($coupon / $totalInstallments);

        return AmountResult::create($value, $valueWithDiscounts, $customerValue, $tax, $coupon);
    }

}
