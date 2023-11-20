<?php


namespace App\Services\Mundipagg\Calculator\Contracts;

use App\Services\Mundipagg\Calculator\Objects\AmountResult;

interface CalculatorStrategy
{

    /**
     * Calculates all the costs using integer values for money (eg: 1234 for R$12,34)
     *
     * @param  int  $price  Original Plan value in "centavos"
     * @param  int  $coupon  Discount coupon in "centavos"
     * @param  float  $percentSplit  Client percentage participation as usually expressed (eg: 98.76 for 98.76%)
     * @param  int  $clientTaxTransaction  Tax per transaction in "centavos"
     * @param  int  $installmentNumber
     * @param  int  $totalInstallments  Number of installments
     * @return \App\Services\Mundipagg\Calculator\Objects\AmountResult
     */
    public function calculate(
        int $price,
        int $coupon,
        float $percentSplit,
        int $clientTaxTransaction,
        int $installmentNumber = 1,
        int $totalInstallments = 1
    ): AmountResult;
}
