<?php

namespace App\Services\Finances\Split;

use App\Payment;
use App\Plan;
use App\Repositories\Split\PaymentPlanSplitBuilder;
use App\Repositories\Split\PaymentPlanSplitRepository;
use App\Services\Mundipagg\Objects\OrderResult;
use App\Services\Mundipagg\Objects\ProducerSplitResult;
use Illuminate\Support\Facades\Log;

/**
 * Class DetailedSplitService process and stores detailed information after producer's sharing
 *
 * @package App\Services\Finances\Split
 */
class DetailedSplitService
{

    /**
     * @param  \App\Payment|null  $lastPayment
     * @param  \App\Services\Mundipagg\Objects\OrderResult|null  $orderResult
     * @param  array  $paymentIds
     */
    public static function create(
        string $platformId,
        int $clientId,
        array $paymentIds,
        ?string $orderCode,
        ?OrderResult $orderResult
    ): array {
        $paymentPlanSplitBuilder = new PaymentPlanSplitBuilder();
        $paymentPlanSplitBuilder->setPlatformId($platformId)->setClientId($clientId)->setOrderCode($orderCode);

        $producerSplits = collect($orderResult->getProducerSplits());
        $producerShares = $producerSplits->flatMap(fn(ProducerSplitResult $item) => $item->getProducerShare());

        $paymentPlans = PaymentPlanSplitRepository::getPaymentPlansByPaymentIds($paymentIds);

        Log::withContext([
            'orderCode' => $orderCode,
            //'paymentPlans' => $paymentPlans->pluck('id') ?? null,
            'payment_plans_count' => $paymentPlans->count(),
        ]);

        if ($paymentPlans->count() == 0) {
            Log::warning('DetailedSplitService::create - no payment plans found',  [
                'absent_payment_plan' => true,
            ]);
        }

        $totalCustomerValue = $paymentPlans->sum('customer_value');

        $offset = 0;

        $rows = [];
        foreach ($paymentPlans as $index => $paymentPlan) {
            $planId = $paymentPlan->plan_id;
            $productId = Plan::findOrFail($planId)->product_id;

            if ($index != 0 && $paymentPlan->type == 'product') {
                $offset++; // increase offset for n-th card
            }

            /** @var  ProducerSplitResult $producerSplitResult */
            $producerSplitResult = $producerSplits->offsetGet($offset);

            $affiliateShare = $producerSplitResult->getAffiliateShareByPlan($planId);

            $affiliateValue = ($affiliateShare->amount ?? 0) / 100;

            $totalPlanCustomerValue = $paymentPlans->where('plan_id', $planId)->sum('customer_value')
                - $affiliateValue;

            $customerValue = $paymentPlan->customer_value - $affiliateValue;

            $producers = $producerShares->where('planId', $planId)->sortBy('planId');

            $ratio = $customerValue / $totalPlanCustomerValue;

            $groupByProducer = $producers->groupBy('producerId');

            $totalAnticipated = 0;
            foreach ($groupByProducer as $producerId => $producer) {
                $producerAnticipation = $producer->sum('anticipation');

                $anticipation = (int) round($ratio * $producerAnticipation, 0, PHP_ROUND_HALF_DOWN);

                $totalAnticipated += $anticipation;

                $producerPercent = $producer[0]->percent;
                $producerAmount = round($customerValue * 100 * $producerPercent / 100, 0, PHP_ROUND_HALF_DOWN);

                $producerRow = $paymentPlanSplitBuilder->createProducerSplit(
                    $productId,
                    $planId,
                    $paymentPlan->id,
                    $producerPercent,
                    $producerAmount,
                    $anticipation,
                    $producer[0]->producerProductId
                );

                array_push($rows, $producerRow);
            }

            $totalRatio = $customerValue / $totalCustomerValue;

            $totalAnticipation = $producerSplits->sum(fn(ProducerSplitResult $item) => $item->getAnticipationAmount());

            $proportionalAnticipation = (int) round($totalAnticipation * $totalRatio, 0, PHP_ROUND_HALF_DOWN);

            $proportionalClientAnticipation = $proportionalAnticipation - $totalAnticipated;

            $clientPercent = $producerSplitResult->getClientPercentShareByPlan($planId) ?? 100;

            $clientAmount = round($customerValue * 100 * $clientPercent / 100, 0, PHP_ROUND_HALF_DOWN);

            $clientRow = $paymentPlanSplitBuilder->createClientSplit(
                $productId,
                $planId,
                $paymentPlan->id,
                $clientPercent,
                $clientAmount,
                $proportionalClientAnticipation
            );

            array_push($rows, $clientRow);

            $affiliatePlanPercent = $affiliateShare->percent ?? 0;
            $affiliatePlanAmount = $affiliateShare->amount ?? 0;
            $affiliatePlanAnticipation = $affiliateShare->anticipation ?? 0;

            if ($affiliatePlanPercent != 0 || $affiliatePlanAmount != 0 || $affiliatePlanAnticipation != 0) {
                // skip when affiliate not participates in this plan
                $affiliateRow = $paymentPlanSplitBuilder->createAffiliateSplit(
                    $productId,
                    $planId,
                    $paymentPlan->id,
                    $affiliatePlanPercent,
                    $affiliatePlanAmount,
                    $affiliatePlanAnticipation,
                    $affiliateShare->getContractId()
                );

                array_push($rows, $affiliateRow);
            }

            $xgrowValue = $paymentPlan->tax_value * 100;

            $xgrowRow = $paymentPlanSplitBuilder->createXgrowSplit(
                $productId,
                $planId,
                $paymentPlan->id,
                $xgrowValue,
                -$proportionalAnticipation
            );

            array_push($rows, $xgrowRow);
        }

        if (count($rows) == 0) {
            Log::warning('DetailedSplitService::create - no rows found',  [
                'absent_payment_plan_split' => true,
            ]);
        }

        return $rows;
    }


}
