<?php

namespace App\Services\Mundipagg;

use App\Client;
use App\Payment;
use App\PaymentPlan;
use App\PaymentPlanSplit;
use App\Platform;
use App\Producer;
use App\Services\Finances\Objects\Coin;
use App\Services\Finances\Objects\PriceTag;
use App\Services\Finances\Split\Calculator\Objects\XgrowSplitResult;
use App\Services\Finances\Split\Calculator\XgrowSplit;
use App\Services\Finances\Split\Calculator\XgrowSplitNolimit;
use App\Services\Finances\Split\DetailedSplitService;
use App\Services\Mundipagg\Objects\AffiliateShare;
use App\Services\Mundipagg\Objects\OrderResult;
use App\Services\Mundipagg\Objects\ProducerShare;
use App\Services\Mundipagg\Objects\ProducerSplitResult;
use App\Services\Pagarme\Calculator\AnticipationCalculator;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use MundiAPILib\Models\GetOrderResponse;

class SplitService
{

    public const UNLIMITED_INACTIVE = 0; // disabled
    public const UNLIMITED_CALCULATION = 1; // "full price" needs installment calculation
    public const UNLIMITED_PRECALCULATED = 2; // monthly installment already calculated in previous step

    private int $unlimitedMode = self::UNLIMITED_INACTIVE;

    protected Platform $platform;
    protected Client $client;
    protected ?Producer $affiliate = null;

    private bool $isAnticipationEnabled = true;

    public function __construct($platform_id)
    {
        Log::withContext(['platform_id' => $platform_id]);

        $this->platform = Platform::findOrFail($platform_id);
        $this->client = Client::findOrFail($this->platform->customer_id);
    }

    public function withAffiliate(Producer $affiliate): self
    {
        $this->affiliate = $affiliate;
        return $this;
    }

    public function setUnlimitedMode(int $unlimitedMode): self
    {
        $this->unlimitedMode = $unlimitedMode;
        return $this;
    }

    public function disableAnticipation(bool $disableAnticipation = true): self
    {
        $this->isAnticipationEnabled = !$disableAnticipation;
        return $this;
    }

    /**
     *
     *
     * @todo Change $value and $valueWithInterest to int (amount)
     *
     * @param  string  $value
     * @param  float  $valueWithInterest
     * @param  \App\Services\Finances\Objects\PriceTag  $mainPriceTag
     * @param  array  $orderBumpPriceTags
     * @param  int  $totalInstallments
     * @param  int  $currentNoLimitInstallment
     * @return \App\Services\Mundipagg\Objects\ProducerSplitResult
     */
    public function getPaymentSplit(
        string $value,
        float $valueWithInterest,
        PriceTag $mainPriceTag,
        array $orderBumpPriceTags = [],
        int $totalInstallments = 1,
        int $currentNoLimitInstallment = 1
    ): ProducerSplitResult {
        $amount = round($value * 100);

        $amountWithInterest = round($valueWithInterest * 100);

        $transactionTax = $this->platform->client->tax_transaction ?? 1.5;

        $transactionTaxAmount = round($transactionTax * 100);

        $percentSplit = $this->client->percent_split;

        $priceTags = collect([$mainPriceTag])->merge($orderBumpPriceTags);

        if ($this->unlimitedMode) {
            $xgrowSplitResult = XgrowSplitNolimit::calculate(
                $priceTags->sum(fn(PriceTag $priceTag): int => $priceTag->getAmount()),
                $amountWithInterest,
                $amount,
                $transactionTaxAmount,
                $percentSplit,
                $currentNoLimitInstallment,
                $totalInstallments
            );
        } else {
            $xgrowSplitResult = XgrowSplit::calculate($amountWithInterest, $amount, $transactionTaxAmount, $percentSplit);
        }

        //$anticipation = $this->isAnticipationEnabled && $this->platform->client->is_default_antecipation_tax
        //    ? $this->calcAnticipation($totalInstallments ?? 1, $customerAmount)
        //    : Coin::fromInt(0);

        $anticipation = 0;

        $producerSplitService = new ProducerSplitService();
        if ($this->unlimitedMode == self::UNLIMITED_CALCULATION) {
            $producerSplitService->setInstallmentsForUnlimiteSell($totalInstallments);
        }

        if ($this->affiliate) {
            $producerSplitService->withAffiliateId($this->affiliate->id);
        }

        $splitResult = $producerSplitService->calculateSplit($xgrowSplitResult, $mainPriceTag, $orderBumpPriceTags);

        $metadata = [
            'customer_value' => $xgrowSplitResult->getCustomerAmount() / 100,
            'service_value' => $xgrowSplitResult->getServiceAmount() / 100,
            'plans_value' => $value,
            'price' => $valueWithInterest,
            'tax_value' => $xgrowSplitResult->getTaxAmount() / 100,
            'antecipation_value' => $anticipation,
        ];

        $splitResult->setMetadata($metadata);

        return $splitResult;
    }


    public function getPrecalculatedPlanSplit(Payment $payment, array $metadata): ProducerSplitResult
    {
        Log::info('Using precalculated payment_plan_split', [
            'payment_id' => $payment->id,
        ]);

        $anticipation = 0; // no anticipation

        $paymentPlans = PaymentPlan::query()
            ->whereIn('status', [
                PaymentPlan::STATUS_PENDING, // regular charge
                PaymentPlan::STATUS_FAILED, // charge ruler
            ])
            ->where('payment_id', $payment->id)
            ->get();

        $paymentPlansIds = $paymentPlans->pluck('id')->toArray();
        $paymentPlanSplits = PaymentPlanSplit::whereIn('payment_plan_id', $paymentPlansIds)->get();

        if ($paymentPlanSplits->count() == 0) {
            $paymentPlanSplits = $this->createAndLoadMissingPaymentPlanSplit($payment, $paymentPlans);
        }

        $xgrowSplits = $paymentPlanSplits->where('type', PaymentPlanSplit::SPLIT_TYPE_XGROW);
        $affiliateSplits = $paymentPlanSplits->where('type', PaymentPlanSplit::SPLIT_TYPE_AFFILIATE);
        $clientSplits = $paymentPlanSplits->where('type', PaymentPlanSplit::SPLIT_TYPE_CLIENT);
        $producerSplits = $paymentPlanSplits->where('type', PaymentPlanSplit::SPLIT_TYPE_PRODUCER);

        $splitResult = new ProducerSplitResult();
        $splitResult->setAnticipationAmount($anticipation);

        $priceAmount = round($paymentPlans->sum('plan_price') * 100);
        $valueAmount = round($paymentPlans->sum('plan_value') * 100);
        $taxAmount = round($paymentPlans->sum('tax_value') * 100);

        // xgrow
        $xgrowAmount = $priceAmount - $valueAmount + $taxAmount;
        $splitResult->setFinalXgrowAmount($xgrowAmount);

        // affiliate
        foreach ($affiliateSplits as $affiliateSplit) {
            $contract = $affiliateSplit->producerProduct;

            $affiliateAmount = round($affiliateSplit->value * 100);

            $affiliateShare = new AffiliateShare(
                $affiliateSplit->producer_product_id,
                $affiliateSplit->percent,
                $affiliateAmount,
                $anticipation
            );

            $splitResult->setAffiliateId($contract->producer_id);
            $splitResult->addAffiliateShareByPlan($affiliateSplit->plan_id, $affiliateShare);
        }

        // co-producer
        foreach ($producerSplits as $producerSplit) {
            $contract = $producerSplit->producerProduct;

            $producerAmount = round($producerSplit->value * 100);

            $producerShare = new ProducerShare(
                $producerSplit->producer_product_id,
                $contract->producer_id,
                $producerSplit->plan_id,
                $producerSplit->product_id,
                $producerSplit->percent,
                $producerAmount,
                $anticipation
            );

            $splitResult->addProducerShare($producerShare);
        }

        // client (owner)
        $clientAmount = round($clientSplits->sum('value') * 100);

        foreach ($clientSplits as $clientRow) {
            $splitResult->setClientPercentShareByPlan($clientRow->plan_id, $clientRow->percent);
        }

        //$splitResult->setCustomerAmount($clientAmount); // customer amount not used in precalculated
        $splitResult->setFinalClientAmount($clientAmount);
        $splitResult->setMetadata($metadata);

        return $splitResult;
    }

    /**
     * @deprecated anticipation not used anymore
     */
    protected function calcAnticipation(int $installments, int $amount): Coin
    {
        $anticipationDate = Carbon::now()->addDays(30); // D+30

        $anticipation = (new AnticipationCalculator())
            ->calculateAnticipation($anticipationDate, $amount, $installments);

        return Coin::fromInt($anticipation);
    }

    /**
     * @param  \App\Payment  $payment
     * @param $paymentPlans
     * @return \Illuminate\Support\Collection
     */
    private function createAndLoadMissingPaymentPlanSplit(Payment $payment, $paymentPlans): Collection
    {
        $paymentPlansIds = $paymentPlans->pluck('id')->toArray();

        Log::error('payment_plan_split not found', [
            'payment_id' => $payment->id ?? null,
            'payment_plan_ids' => $paymentPlansIds ?? null,
        ]);

        $affiliateId = $this->getAffiliateId($payment);

        $mainPlan = $paymentPlans->where('type', 'product')->first();
        $orderBumps = $paymentPlans->where('type', 'order_bump');

        $mainPriceTag = PriceTag::fromDecimal($mainPlan->plan_id, $mainPlan->plan_value);

        $orderBumpPriceTags = [];
        foreach ($orderBumps as $orderBump) {
            $orderBumpPriceTags[] = PriceTag::fromDecimal($orderBump->plan_id, $orderBump->plan_value);
        }

        foreach ($paymentPlans as $paymentPlan) {
            $platform = Platform::find($payment->platform_id);
            $client = $platform->client;
            $xgrowSplitResult = new XgrowSplitResult(
                $paymentPlan->customer_value,
                $paymentPlan->tax_value,
                $paymentPlan->tax_value,
                (int) round($client->tax_transaction * 100)
            );

            $producerSplitService = new ProducerSplitService();
            if ($affiliateId) {
                $producerSplitService->withAffiliateId($affiliateId);
            }

            $splitResult = $producerSplitService->calculateSplit(
                $xgrowSplitResult,
                $mainPriceTag,
                $orderBumpPriceTags
            );

            DetailedSplitService::create(
                $payment->platform_id,
                $platform->client->id,
                [$payment->id],
                $payment->order_code,
                OrderResult::fromMundipagg(new GetOrderResponse(), [$splitResult])
            );
        }

        // reload models from database
        $paymentPlanSplits = PaymentPlanSplit::whereIn('payment_plan_id', $paymentPlansIds)->get();

        if ($paymentPlanSplits->count() == 0) {
            // throw exception if payment_plan_split is still missing
            throw new \RuntimeException('payment_plan_split not found', 404);
        }

        return $paymentPlanSplits;
    }

    private function getAffiliateId(Payment $payment): ?int
    {
        // get first payment of "sem-limite"
        $firstPayment = Payment::query()
            ->where('order_code', $payment->order_code)
            ->orderBy('installment_number')
            ->first();

        $paymentPlans = PaymentPlan::query()
            ->where('status', PaymentPlan::STATUS_PAID)
            ->where('payment_id', $firstPayment->id)
            ->get();

        $paymentPlansIds = $paymentPlans->pluck('id')->toArray();
        $affiliatePaymentPlanSplit = PaymentPlanSplit::query()
            ->whereIn('payment_plan_id', $paymentPlansIds)
            ->where('type', PaymentPlanSplit::SPLIT_TYPE_AFFILIATE)
            ->first();

        return $affiliatePaymentPlanSplit->producer_product_id ?? null;
    }

}
