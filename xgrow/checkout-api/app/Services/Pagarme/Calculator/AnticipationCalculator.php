<?php

namespace App\Services\Pagarme\Calculator;

use Carbon\Carbon;
use Exception;
use RangeException;

/**
 * This class uses Pagar.me anticipation calculation.
 */
class AnticipationCalculator
{

    private static $monthlyAnticipationTax = 1.52; // anticipation tax as usually expressed (eg: 1.5 = 1,5%)

    private static $firstSettlementDays = 1; // number of days before anticipation date

    private function dailyAnticipationTax(): float
    {
        return self::$monthlyAnticipationTax / 30;
    }

    private function dailyAnticipationFactor(): float
    {
        return $this->dailyAnticipationTax() / 100;
    }

    /**
     * Calculate the anticipation based on a given anticipation date, amount and installments.
     *
     * @param  \Carbon\Carbon  $anticipationDate  The anticipation date.
     * @param  int  $amount Total amount (in "centavos").
     * @param  int  $installments Number of installments.
     * @return float The anticipation amount (in "centavos").
     * @throws \Exception
     */
    public function calculateAnticipation(Carbon $anticipationDate, int $amount, int $installments): float
    {
        if ($installments <= 0) {
            throw new RangeException("Installments must be greater than 0");
        }

        if ($installments == 1) {
            return 0;
        }

        $settlementDates = $this->settlementDates($anticipationDate, $installments);

        $anticipationDays = $this->anticipationDays($settlementDates);

        $anticipationPercentages = $this->anticipationPercentages($anticipationDays);

        $amounts = $this->getAmounts($amount, $installments);

        $anticipationTaxes = $this->anticipationTaxes($amounts, $anticipationPercentages);

        return round($this->totalAnticipationTax($anticipationTaxes));
    }


    /**
     * Create an array of settlement dates ("Data de liquidação") as Carbon objects based on an initial anticipation
     * date and a number of installments.
     *
     * Eg: []
     *
     * @param  \Carbon\Carbon  $anticipationDate
     * @param  int  $installments
     * @return Carbon[] An array of Carbon objects (settlement dates) containing #
     */
    private function settlementDates(Carbon $anticipationDate, int $installments): array
    {
        if ($installments <= 1) {
            throw new RangeException("Installments must be greater than 1 to have a settlement date");
        }

        $dates = [
            0 => $anticipationDate->clone()->addDays(self::$firstSettlementDays),
        ];

        foreach (range(1, $installments - 1) as $i) {
            $previous = $i - 1;
            $daysInMonthOnPrevious = $dates[$previous]->daysInMonth;
            $dates[$i] = $dates[$previous]->clone()->addDays($daysInMonthOnPrevious);
        }

        return $dates;
    }

    /**
     * Number of days between first settlement date and others.
     *
     * @param  array  $settlementDates
     * @return int[]
     */
    private function anticipationDays(array $settlementDates): array
    {
        $anticipationDays = [
            0 => self::$firstSettlementDays,
        ];

        $first = array_shift($settlementDates);
        foreach ($settlementDates as $date) {
            $anticipationDays[] = $date->diffInDays($first);
        }

        return $anticipationDays;
    }

    /**
     * Array of percentage taxes for each installment.
     *
     * @param  array  $intervalsInDays
     * @return float[]
     */
    private function anticipationPercentages(array $intervalsInDays): array
    {
        $anticipationTaxes = [];
        foreach ($intervalsInDays as $interval) {
            $anticipationTaxes[] = $this->dailyAnticipationFactor() * $interval;
        }

        return $anticipationTaxes;
    }


    /**
     * This method uses Pagar.me algorithm to calculate each installment amount (first installment lower than remaining)
     *
     * @param  int  $amount
     * @param  int  $installments
     * @return int[]
     */
    private function getAmounts(int $amount, int $installments): array
    {
        if ($installments <= 0) {
            throw new RangeException("Installments must be greater than 0");
        }

        $amountPerInstallment = ceil($amount / $installments); // apparently, Pagar.me uses ceil() to calculate installments

        $firstInstallment = $amount - $amountPerInstallment * ($installments - 1);

        $others = array_fill(0, $installments - 1, $amountPerInstallment);

        return array_merge([$firstInstallment], $others);
    }

    /**
     * Get a list of anticipation taxes for each installment.
     *
     * @param  int[]  $installmentsAmount
     * @param  float[]  $anticipationPercentages
     * @return float[]  $anticipationTax for each installment
     * @throws \Exception
     */
    private function anticipationTaxes(array $installmentsAmount, array $anticipationPercentages): array
    {
        if (count($installmentsAmount) !== count($anticipationPercentages)) {
            throw new Exception("Installments amount and anticipation percentages must have the same length");
        }

        $anticipationTaxes = [];
        foreach ($installmentsAmount as $i => $amount) {
            $anticipationTaxes[] = $amount * $anticipationPercentages[$i];
        }

        return $anticipationTaxes;
    }

    /**
     * Calculate the total anticipation tax.
     *
     * @param  float[]  $anticipationTaxes
     * @return float
     */
    private function totalAnticipationTax(array $anticipationTaxes): float
    {
        return array_sum($anticipationTaxes);
    }


}
