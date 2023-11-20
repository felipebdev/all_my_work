<?php


namespace App\Services\Mundipagg\Objects;


use App\Services\Finances\Split\Calculator\Objects\XgrowSplitResult;
use Illuminate\Support\Collection;

/**
 * Class ProducerSplitResult holds the split for a single payment
 *
 * @package App\Services\Mundipagg\Objects
 */
class ProducerSplitResult
{
    private int $anticipationAmount; // total client anticipation

    private int $customerAmount; // client amount before sharing (without anticipation)

    private int $taxAmount;

    private ?string $affiliateId = null;

    /**
     * @var array<string, \App\Services\Mundipagg\Objects\AffiliateShare>
     */
    private array $affiliateShare = [];

    /**
     * @var array<\App\Services\Mundipagg\Objects\ProducerShare>
     */
    private array $producers = [];

    private int $finalXgrowAmount; // xgrow amount with anticipation

    private int $finalClientAmount;

    private array $metadata = [];

    // global

    public function getAnticipationAmount(): int
    {
        return $this->anticipationAmount;
    }

    public function setAnticipationAmount(int $anticipationAmount): ProducerSplitResult
    {
        $this->anticipationAmount = $anticipationAmount;
        return $this;
    }

    //// customer (client + coproducer + affiliate)

    public function getCustomerAmount(): int
    {
        return $this->customerAmount;
    }

    public function setCustomerAmount(int $customerAmount): ProducerSplitResult
    {
        $this->customerAmount = $customerAmount;
        return $this;
    }

    ////// client per plan

    public function getClientPercentShareByPlan($id): ?float
    {
        return $this->clientPercentShare[$id] ?? null;
    }

    public function setClientPercentShareByPlan(string $planId, float $clientPercentShare): ProducerSplitResult
    {
        $this->clientPercentShare[$planId] = $clientPercentShare;
        return $this;
    }

    // Xgrow

    public function getXgrowAmount(): int
    {
        return $this->xgrowAmount;
    }

    public function setXgrowAmount(int $xgrowAmount): ProducerSplitResult
    {
        $this->xgrowAmount = $xgrowAmount;
        return $this;
    }

    public function getTaxAmount(): int
    {
        return $this->taxAmount;
    }

    /**
     * @param  int  $taxAmount
     * @return ProducerSplitResult
     */
    public function setTaxAmount(int $taxAmount): ProducerSplitResult
    {
        $this->taxAmount = $taxAmount;
        return $this;
    }

    // affiliate

    public function setAffiliateId(string $affiliateId): ProducerSplitResult
    {
        $this->affiliateId = $affiliateId;
        return $this;
    }

    public function getAffiliateId(): ?string
    {
        return $this->affiliateId;
    }

    public function addAffiliateShareByPlan(string $planId, AffiliateShare $affiliateShare): void
    {
        $this->affiliateShare[$planId] = $affiliateShare;
    }

    public function getAffiliateShareByPlan(string $planId): ?AffiliateShare
    {
        return $this->affiliateShare[$planId] ?? null;
    }

    public function getAffiliateTotalAnticipation(): int
    {
        return $this->getAffiliateShareCollection()->sum(fn(AffiliateShare $affiliateShare) => $affiliateShare->anticipation);
    }

    public function getAffiliateTotalAmount(): int
    {
        return $this->getAffiliateShareCollection()->sum(fn(AffiliateShare $affiliateShare) => $affiliateShare->amount);
    }

    private function getAffiliateShareCollection(): Collection
    {
        return collect($this->affiliateShare);
    }

    public function getAffiliateFinalAmount(): int
    {
        return $this->getAffiliateTotalAmount() - $this->getAffiliateTotalAnticipation();
    }

    // (co)producer

    public function addProducerShare(ProducerShare $producerShare): ProducerSplitResult
    {
        $this->producers[] = $producerShare;
        return $this;
    }

    public function getProducerShare(): Collection
    {
        return collect($this->producers);
    }

    public function getProducerTotalAmount(): int
    {
        return $this->getProducerShare()->sum(fn(ProducerShare $producerShare) => $producerShare->amount);
    }

    public function getProducerTotalAnticipation(): int
    {
        return $this->getProducerShare()->sum(fn(ProducerShare $producerShare) => $producerShare->anticipation);
    }

    /**
     * Aggregate values by producer (eg: main plan + order bump)
     *
     * @return array<string,int> Array key as producerId, value as aggregated producer value
     */
    public function aggregateAmountByProducer(): array
    {
        $results = $this->getProducerShare();

        $split = [];

        foreach ($results->groupBy('producerId') as $producerId => $producer) {
            $finalProducerAmount = $producer->sum('amount');
            $finalProducerAnticipation = $producer->sum('anticipation');
            $split[$producerId] = $finalProducerAmount + $finalProducerAnticipation;
        }

        return $split;
    }

    // total

    public function getFinalXgrowAmount(): int
    {
        return $this->finalXgrowAmount;
    }

    public function setFinalXgrowAmount(int $finalXgrowAmount): ProducerSplitResult
    {
        $this->finalXgrowAmount = $finalXgrowAmount;
        return $this;
    }

    public function getFinalClientAmount(): int
    {
        return $this->finalClientAmount;
    }

    public function setFinalClientAmount(int $finalClientAmount): ProducerSplitResult
    {
        $this->finalClientAmount = $finalClientAmount;
        return $this;
    }

    // other

    public function getTotalAmount(): int
    {
        return $this->getFinalXgrowAmount()
            + $this->getFinalClientAmount()
            + $this->getProducerTotalAmount()
            + $this->getAffiliateTotalAmount();
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function setMetadata(array $metadata): ProducerSplitResult
    {
        $this->metadata = $metadata;
        return $this;
    }

}
