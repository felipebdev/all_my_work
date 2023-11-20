<?php

namespace App\Services\Mundipagg\Calculator\Strategies;

use App\Payment;
use App\Plan;

class CalculatorLegacy
{
    /*
            [
                $mainPrice,
                $mainCustomerValue,
                $mainTax,
                $mainCoupon,
                $mainPlanValue
            ] = Calculator::v1(
                    $mainPlan->price,
                    $mainPlanValue,
                    $mainCoupon,
                    $payment->type,
                    $payment->installments,
                    $subscriber->platform->client->percent_split,
                    $clientTaxTransaction
            );
     */

    /**
     * @param $mainPlan
     * @param $mainPlanValue
     * @param $mainCoupon
     * @param  \App\Payment  $payment
     * @param  \App\Subscriber  $subscriber
     * @param $clientTaxTransaction
     * @return array
     */
    public static function v1(
        $mainPlanPrice,
        $mainPlanValue,
        $mainCoupon,
        $paymentType,
        $installments,
        $percentSplit,
        $clientTaxTransaction
    ): array {
        $valueWithDiscounts = $mainPlanValue - $mainCoupon;

        $mainPrice = (new Plan())->
            getInstallmentValue(($mainPlanValue - $mainCoupon), $installments) * $installments;

        $customerValue = ($percentSplit / 100) * ($mainPlanValue - $mainCoupon);
        $taxValue = ((100 - $percentSplit) / 100) * ($mainPlanPrice - $mainCoupon);


        $mainCustomerValue = $paymentType == Payment::TYPE_UNLIMITED
            ? round($customerValue, 2)
            : round($customerValue - $clientTaxTransaction, 2);

        $mainTax = $paymentType == Payment::TYPE_UNLIMITED
            ? round($taxValue, 2)
            : round($taxValue + $clientTaxTransaction, 2);

        if (round((($mainCustomerValue + $mainTax) - $mainPlanValue), 2) == 0.01) {
            $mainTax = $mainTax - 0.01;
        }

        if ($paymentType == Payment::TYPE_UNLIMITED) {
            $mainTax = ($mainTax / $installments) + $clientTaxTransaction;
            $mainPrice = $mainPrice / $installments;
            $mainCustomerValue = ($mainCustomerValue / $installments) - $clientTaxTransaction;
            $mainCoupon = $mainCoupon / $installments;
            $mainPlanValue = $mainPlanValue / $installments;
        }

        return array($valueWithDiscounts, $mainPrice, $mainCustomerValue, $mainTax, $mainCoupon, $mainPlanValue);
        //return array($mainPrice, $mainCustomerValue, $mainTax, $mainCoupon, $mainPlanValue, $percentSplit);
    }

}
