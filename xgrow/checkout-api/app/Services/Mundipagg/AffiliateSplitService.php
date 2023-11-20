<?php

namespace App\Services\Mundipagg;

use App\Exceptions\BadConfigurationException;
use App\Plan;
use App\ProducerProduct;
use App\Repositories\Affiliation\AffiliateProductRepository;
use App\Services\Finances\Objects\PriceTag;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Calculate Affiliate values based on active contracts
 */
class AffiliateSplitService
{

    private PriceTag $mainPriceTag;

    private Collection $orderBumpPriceTags;

    private int $clientAmount;

    private int $transactionTax;

    private Collection $contracts;

    private AffiliateProductRepository $affiliateProductRepository;

    /**
     * @param  \App\Services\Finances\Objects\PriceTag  $mainPriceTag
     * @param  \Illuminate\Support\Collection  $orderBumpPriceTags
     * @param  string  $affiliateId
     * @param  int  $clientAmount
     */
    public function __construct(
        PriceTag $mainPriceTag,
        Collection $orderBumpPriceTags,
        string $affiliateId,
        int $clientAmount,
        int $transactionTax
    )
    {
        $this->mainPriceTag = $mainPriceTag;

        $this->orderBumpPriceTags = $orderBumpPriceTags;

        $this->affiliateProductRepository = new AffiliateProductRepository();

        $this->clientAmount = $clientAmount;

        $this->transactionTax = $transactionTax;

        $planIds = $this->priceTags()->map(fn(PriceTag $priceTag) => $priceTag->getId())->toArray();

        $this->contracts = $this->affiliateProductRepository->getAffiliateActiveContractsByPlanIds(
            $affiliateId,
            $planIds
        );

        Log::withContext(['plan_ids' => $planIds]);

        if ($this->contracts->count() === 0) {
            Log::warning('affiliate:no-contracts-found');
        }
    }

    /**
     * @param  string  $planId
     * @return int|null
     * @throws \App\Exceptions\BadConfigurationException
     */
    public function getContractIdByPlanId(string $planId): ?int
    {
        $contract = $this->filterContractByPlanId($planId);
        return $contract->id ?? null;
    }

    public function getAmountForPlanId(string $planId): int
    {
        $percent = $this->getPercentByPlanId($planId);

        if (!$percent) {
            return 0;
        }

        $decimalPercent = $percent / 100;

        // calculate proportional
        $proportional = $this->getPriceTagAmountByPlanId($planId) / $this->getPriceTagAmountTotal();

        if ($planId == $this->mainPriceTag->getId()) {
            // adjustment associated with transaction tax only on Main Product
            $orderBumpsAmount = $this->orderBumpPriceTags->sum(fn(PriceTag $priceTag): int => $priceTag->getAmount());
            $mainAmount = $this->mainPriceTag->getAmount();

            // the tax adjustment is proportional to OB/MainProduct ratio
            $proportionalTaxAdjustment =  $orderBumpsAmount / $mainAmount;

            $adjustedPlanAmount = $this->clientAmount - ($this->transactionTax * $proportionalTaxAdjustment);
        } else {
            $adjustedPlanAmount = $this->clientAmount + $this->transactionTax;
        }

        $amount = $adjustedPlanAmount * $proportional * $decimalPercent;

        return round($amount);
    }

    public function getPercentByPlanId(string $planId): float
    {
        $contract = $this->filterContractByPlanId($planId);

        if (!$contract) {
            return 0; // contract not found for this planId
        }

        return $contract->percent ?? 0;
    }

    public function getTotalAmount(): int
    {
        return $this->priceTags()->sum(fn(PriceTag $priceTag): int => $this->getAmountForPlanId($priceTag->getId()));
    }

    private function getPriceTagAmountByPlanId(string $planId): int
    {
        $priceTag = $this->priceTags()->first(fn(PriceTag $priceTag): bool => $priceTag->getId() == $planId);

        if (!$priceTag) {
            return 0;
        }

        return $priceTag->getAmount() ?? 0;
    }

    private function getPriceTagAmountTotal(): int
    {
        return $this->priceTags()->sum(fn(PriceTag $priceTag): int => $priceTag->getAmount());
    }

    private function filterContractByPlanId(string $planId): ?ProducerProduct
    {
        $productId = Plan::find($planId)->product_id;

        $contract = $this->contracts->where('product_id', $productId);

        if ($contract->count() > 1) {
            $ids = $contract->pluck('id')->implode(', ') ?? '';
            Log::error('affiliate:multiple-active-contracts-found', [
                'product_id' => $productId,
                'contract_ids' => $ids,
            ]);
            throw new BadConfigurationException("Affiliate has multiple active contracts for product {$productId}: {$ids}");
        }

        return $contract->first();
    }

    private function priceTags(): Collection
    {
        return collect([$this->mainPriceTag])->merge($this->orderBumpPriceTags);
    }

}
