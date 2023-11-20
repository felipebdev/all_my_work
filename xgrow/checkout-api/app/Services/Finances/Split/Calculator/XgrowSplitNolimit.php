<?php

namespace App\Services\Finances\Split\Calculator;

use App\Services\Finances\Objects\Coin;
use App\Services\Finances\Split\Calculator\Objects\XgrowSplitResult;

/**
 * Class XgrowSplit calculates the split for Xgrow and client (owner) on "Sem-limite"
 *
 * @package App\Services\Finances\Split\Calculator
 */
class XgrowSplitNolimit
{

    /**
     * @param  int  $originalAmount  Original amount without interests (plans price)
     * @param  int  $installmentAmountWithInterest  Current installment amount with interests
     * @param  int  $installmentAmount  Current installment amount without interests
     * @param  int  $transactionTaxAmount  Transaction tax (fixed tax) for client
     * @param  float  $percentSplit  Client percent split
     * @param  int  $currentInstallment  Number of current installment
     * @param  int  $totalInstallments  Total number of installments
     * @return \App\Services\Finances\Split\Calculator\Objects\XgrowSplitResult
     */
    public static function calculate(
        int $originalAmount,
        int $installmentAmountWithInterest,
        int $installmentAmount,
        int $transactionTaxAmount,
        float $percentSplit,
        int $currentInstallment,
        int $totalInstallments
    ): XgrowSplitResult {
        $xgrowPercent = (100 - $percentSplit) / 100;

        $totalTransactionTax = $totalInstallments * $transactionTaxAmount;

        $totalTax = $originalAmount * $xgrowPercent + $totalTransactionTax;

        $taxAmount = Coin::fromInt($totalTax)
            ->installmentNumber($currentInstallment, $totalInstallments)
            ->getAmount();

        $customerAmount = $installmentAmount - $taxAmount;

        $serviceAmount = $installmentAmountWithInterest - $customerAmount;

        return new XgrowSplitResult($customerAmount, $serviceAmount, $taxAmount, $totalTransactionTax);
    }
}
