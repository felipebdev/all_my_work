<?php

namespace App\Services\Mundipagg\Objects;

#[Immutable]
class AffiliateShare
{

    public int $contractId;
    public float $percent = 0.0;
    public int $amount = 0;
    public int $anticipation = 0;

    public static function create(int $contractId, float $percent, int $amount, int $anticipation = 0): self
    {
        return new self($contractId, $percent, $amount, $anticipation);
    }

    public function __construct(int $contractId, float $percent, int $amount, int $anticipation)
    {
        $this->percent = $percent;
        $this->amount = $amount;
        $this->anticipation = $anticipation;
        $this->contractId = $contractId;
    }

    public function getContractId(): int
    {
        return $this->contractId;
    }

    public function getPercent(): float
    {
        return $this->percent;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getAnticipation(): int
    {
        return $this->anticipation;
    }

}
