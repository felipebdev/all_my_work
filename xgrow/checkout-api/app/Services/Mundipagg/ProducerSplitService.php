<?php

namespace App\Services\Mundipagg;

use App\Plan;
use App\Producer;
use App\ProducerProduct;
use App\Services\Finances\Objects\PriceTag;
use App\Services\Finances\Split\Calculator\Objects\XgrowSplitResult;
use App\Services\Finances\Split\Calculator\RevenueAccountant;
use App\Services\Mundipagg\Objects\AffiliateShare;
use App\Services\Mundipagg\Objects\ProducerShare;
use App\Services\Mundipagg\Objects\ProducerSplitResult;
use Illuminate\Support\Collection;

/**
 * Class ProducerSplitService calculates split among client(owner)/producers based on active contracts
 *
 * @package App\Services\Mundipagg
 */
class ProducerSplitService
{

    private ?int $installments = null;
    private ?string $affiliateId = null;

    /**
     * Installments must be set for unlimited sell
     *
     * @param  int|null  $installments
     * @return $this
     */
    public function setInstallmentsForUnlimiteSell(?int $installments): self
    {
        $this->installments = $installments;
        return $this;
    }

    public function withAffiliateId(string $affiliateId): self
    {
        $this->affiliateId = $affiliateId;
        return $this;
    }

    public function calculateSplit(
        XgrowSplitResult $xgrowSplitResult,
        PriceTag $mainPriceTag,
        array $orderBumpPriceTags = [],
        int $anticipationAmount = 0
    ): ProducerSplitResult {
        $orderBumpPriceTags = collect($orderBumpPriceTags);
        $priceTags = collect([$mainPriceTag])->merge($orderBumpPriceTags);

        $totalOrderAmountWithoutInterest = $priceTags->sum(fn(PriceTag $priceTag) => $priceTag->getAmount());

        $splitResult = new ProducerSplitResult();

        // set values
        $affiliateAnticipation = 0;
        if ($this->affiliateId) {
            $affiliateSplit = new AffiliateSplitService(
                $mainPriceTag,
                $orderBumpPriceTags,
                $this->affiliateId,
                $xgrowSplitResult->getCustomerAmount(),
                $xgrowSplitResult->getTransactionTax()
            );

            $affiliateAmount = $affiliateSplit->getTotalAmount();
            $affiliateAnticipation = round($anticipationAmount * $affiliateAmount / $xgrowSplitResult->getCustomerAmount());

            $splitResult->setAffiliateId($this->affiliateId);

            $priceTags->each(function (PriceTag $priceTag) use ($splitResult, $affiliateSplit) {
                $planId = $priceTag->getId();

                $contractId = $affiliateSplit->getContractIdByPlanId($planId);
                if ($contractId) {
                    $affiliatePercent = $affiliateSplit->getPercentByPlanId($planId);
                    $affiliateAmount = $affiliateSplit->getAmountForPlanId($planId);

                    // @todo fix if anticipation is not zero
                    $affiliateShare = AffiliateShare::create($contractId, $affiliatePercent, $affiliateAmount, 0);

                    $splitResult->addAffiliateShareByPlan($planId, $affiliateShare);
                }
            });
        }

        $splitResult->setAnticipationAmount($anticipationAmount);
        $splitResult->setXgrowAmount($xgrowSplitResult->getServiceAmount());
        $splitResult->setCustomerAmount($xgrowSplitResult->getCustomerAmount());
        $splitResult->setTaxAmount($xgrowSplitResult->getTaxAmount());
        $splitResult->setFinalXgrowAmount($xgrowSplitResult->getServiceAmount() - $anticipationAmount);

        //> dump("Payment of {$amount}");
        //> dump("Xgrow Amount: $xgrowAmount, Client Amount: $clientAmount");

        $anticipationAfterAffiliate = $anticipationAmount - $affiliateAnticipation;

        $producerContracts = $this->getProducerContractsFromPriceTags($priceTags);

        foreach ($priceTags as $priceTag) {
            $planId = $priceTag->getId();
            $planAmount = $priceTag->getAmount();

            if ($this->affiliateId) {
                 $planAmount -= $affiliateSplit->getAmountForPlanId($planId) ?? 0;
            }

            if (!is_null($this->installments)) {
                $planAmount /= $this->installments;
            }

            $ratio = $planAmount / $totalOrderAmountWithoutInterest;

            $proportionalAmount = (int) round($xgrowSplitResult->getCustomerAmount() * $ratio, 0, PHP_ROUND_HALF_DOWN);
            $proportionalAnticipation = (int) round($anticipationAfterAffiliate * $ratio, 0, PHP_ROUND_HALF_DOWN);

            // producers for a single product
            $productId = Plan::findOrFail($priceTag->getId())->product_id;
            $producerProductContracts = $producerContracts->where('product_id', $productId);

            $accountant = new RevenueAccountant();
            foreach ($producerProductContracts as $producerProduct) {
                $accountant->add($producerProduct->producer_id, $producerProduct->percent);
            }

            $splitResult->setClientPercentShareByPlan($planId, $accountant->getRemainderPercentage());

            $revenueShare = $accountant->share($proportionalAmount);
            $anticipationShare = $accountant->share($proportionalAnticipation);

            foreach ($producerProductContracts as $producerProduct) {
                $producerProductId = $producerProduct->id;
                $producerId = $producerProduct->producer_id;
                $percent = $producerProduct->percent;

                $producerAmount = $revenueShare->getAllocationById($producerId);
                $producerAnticipation = $anticipationShare->getAllocationById($producerId);

                $producerShare = new ProducerShare(
                    $producerProductId,
                    $producerId,
                    $priceTag->getId(),
                    $productId,
                    $percent,
                    $producerAmount,
                    $producerAnticipation
                );
                $splitResult->addProducerShare($producerShare);
            }
        }

        $remainderAnticipation = $anticipationAfterAffiliate
            - $splitResult->getProducerTotalAnticipation();

        $remainderClientAmount = $xgrowSplitResult->getCustomerAmount()
            - $splitResult->getProducerTotalAmount()
            - $splitResult->getAffiliateTotalAmount();

        $finalClientAmount = $remainderClientAmount + $remainderAnticipation;

        $splitResult->setFinalClientAmount($finalClientAmount);

        return $splitResult;
    }

    /**
     * @param  \Illuminate\Support\Collection<PriceTag>  $priceTags
     * @return \Illuminate\Support\Collection
     */
    protected function getProducerContractsFromPriceTags(Collection $priceTags): Collection
    {
        $planIds = $priceTags->map(fn(PriceTag $priceTag) => $priceTag->getId());

        $productIds = Plan::whereIn('id', $planIds)->get()->pluck('product_id')->toArray();

        return ProducerProduct::whereIn('product_id', $productIds)
            ->where('status', ProducerProduct::STATUS_ACTIVE)
            ->whereRelation('producer', 'type', Producer::TYPE_PRODUCER)
            ->where(function($query) {
                $query->whereRaw('contract_limit >= CURDATE()');
                $query->orWhereNull('contract_limit');
            })
            ->get();
    }


}
