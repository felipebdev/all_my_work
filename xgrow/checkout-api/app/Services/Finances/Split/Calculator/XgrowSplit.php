<?php

namespace App\Services\Finances\Split\Calculator;

use App\Services\Finances\Split\Calculator\Objects\XgrowSplitResult;

/**
 * Class XgrowSplit calculates the split for Xgrow and Customer (producer + coproducer + affiliate) WITHOUT anticipation
 *
 * @package App\Services\Finances\Split\Calculator
 */
class XgrowSplit
{

    /**
     * Calculate split using the smallest currency unit (eg: 1234 for R$12,34)
     *
     * @param  int  $amountWithInterests  Amount including interests
     * @param  int  $price  Original price
     * @param  int  $transactionTax  Client tax per transaction
     * @param  float  $percentSplit  Percentage as usually expressed (12.34 for 12,34%)
     * @return \App\Services\Finances\Split\Calculator\Objects\XgrowSplitResult
     */
    public static function calculate(
        int $amountWithInterests,
        int $price,
        int $transactionTax,
        float $percentSplit
    ): XgrowSplitResult {
        // round half down customer split (eg: 1234.5 "centavos" (R$12,345) is rounded to 1234 "centavos" (R$12,34)
        $customerSplit = (int) round($percentSplit / 100 * $price, 0, PHP_ROUND_HALF_DOWN);

        // from now on, all math is made using addition/subtraction, no more rounding errors

        $customerValue = $customerSplit - $transactionTax;

        $serviceValue = $amountWithInterests - $customerSplit + $transactionTax;

        $taxValue = $price - $customerSplit + $transactionTax;

        return new XgrowSplitResult($customerValue, $serviceValue, $taxValue, $transactionTax);
    }
}
