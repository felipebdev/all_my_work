<?php

namespace App\Repositories\Split;

use App\PaymentPlanSplit;

class PaymentPlanSplitBuilder
{
    /**
     * Holds a partial Model
     *
     * @var \App\PaymentPlanSplit
     */
    private PaymentPlanSplit $paymentPlanSplit;

    public function __construct()
    {
        $this->paymentPlanSplit = new PaymentPlanSplit();
    }

    public function setClientId($clientId): self
    {
        $this->paymentPlanSplit->client_id = $clientId;
        return $this;
    }

    public function setPlatformId($platformId): self
    {
        $this->paymentPlanSplit->platform_id = $platformId;
        return $this;
    }

    public function setOrderCode($orderCode): self
    {
        $this->paymentPlanSplit->order_code = $orderCode;
        return $this;
    }

    public function createXgrowSplit(
        int $productId,
        int $planId,
        int $paymentPlanId,
        int $amount,
        int $anticipationAmount
    ): PaymentPlanSplit {
        $xgrowSplit = $this->fillBaseModel($productId, $planId, $paymentPlanId, $amount, $anticipationAmount);

        $xgrowSplit->type = PaymentPlanSplit::SPLIT_TYPE_XGROW;
        $xgrowSplit->save();

        return $xgrowSplit;
    }

    public function createAffiliateSplit(
        int $productId,
        int $planId,
        int $paymentPlanId,
        float $percent,
        int $amount,
        int $anticipationAmount,
        int $contractId
    ): PaymentPlanSplit {
        $affiliateSplit = $this->fillBaseModel($productId, $planId, $paymentPlanId, $amount, $anticipationAmount);

        $affiliateSplit->percent = $percent;
        $affiliateSplit->type = PaymentPlanSplit::SPLIT_TYPE_AFFILIATE;
        $affiliateSplit->producer_product_id = $contractId;
        $affiliateSplit->save();

        return $affiliateSplit;
    }

    public function createClientSplit(
        int $productId,
        int $planId,
        int $paymentPlanId,
        float $percent,
        int $amount,
        int $anticipationAmount
    ): PaymentPlanSplit {
        $clientSplit = $this->fillBaseModel($productId, $planId, $paymentPlanId, $amount, $anticipationAmount);

        $clientSplit->percent = $percent;
        $clientSplit->type = PaymentPlanSplit::SPLIT_TYPE_CLIENT;
        $clientSplit->save();

        return $clientSplit;
    }

    public function createProducerSplit(
        int $productId,
        int $planId,
        int $paymentPlanId,
        float $percent,
        int $amount,
        int $anticipationAmount,
        int $producerProductId
    ): PaymentPlanSplit {
        $producerSplit = $this->fillBaseModel($productId, $planId, $paymentPlanId, $amount, $anticipationAmount);

        $producerSplit->percent = $percent;
        $producerSplit->producer_product_id = $producerProductId;
        $producerSplit->type = PaymentPlanSplit::SPLIT_TYPE_PRODUCER;
        $producerSplit->save();

        return $producerSplit;
    }

    private function fillBaseModel(
        int $productId,
        int $planId,
        int $paymentPlanId,
        int $amount,
        int $anticipationAmount
    ): PaymentPlanSplit {
        $model = $this->paymentPlanSplit->replicate();

        $model->product_id = $productId;
        $model->plan_id = $planId;
        $model->payment_plan_id = $paymentPlanId;
        $model->value = $amount / 100;
        $model->anticipation_value = $anticipationAmount / 100;

        return $model;
    }

}
