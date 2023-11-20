<?php

namespace App\Services\Finances\Split\Calculator;

use App\Services\Finances\Split\Calculator\Objects\XgrowSplitResultWithAnticipation;

/**
 * Class XgrowSplit calculates the split for Xgrow and client (owner)
 *
 * @package App\Services\Finances\Split\Calculator
 */
class XgrowSplitWithAnticipation
{

    /**
     * Calculate split using the smallest currency unit (eg: 1234 for R$12,34)
     *
     * @param  int  $amountWithInterests  Amount including interests
     * @param  int  $price  Original price
     * @param  int  $transactionTax  Client tax per transaction
     * @param  int  $anticipation  Anticipation
     * @param  float  $percentSplit  Percentage as usually expressed (12.34 for 12,34%)
     * @return array
     */
    public static function calculate(
        int $amountWithInterests,
        int $price,
        int $transactionTax,
        int $anticipation,
        float $percentSplit
    ): XgrowSplitResultWithAnticipation {
        // round half down customer split (eg: 1234.5 "centavos" (R$12,345) is rounded to 1234 "centavos" (R$12,34)
        $customerSplit = (int) round($percentSplit / 100 * $price, 0, PHP_ROUND_HALF_DOWN);

        // from now on, all math is made using addition/subtraction, no more rounding errors

        $customerValue = $customerSplit - $transactionTax;

        $clientAmount = $customerValue;

        $serviceValue = $amountWithInterests - $customerSplit + $transactionTax;

        $xgrowAmount = $serviceValue;

        $taxValue = $price - $customerSplit + $transactionTax;

        return new XgrowSplitResultWithAnticipation($customerValue, $clientAmount, $serviceValue, $xgrowAmount, $taxValue);
    }
}
