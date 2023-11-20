<?php

namespace App\Services\Finances\Split\Calculator\Objects;

#[Immutable]
class RevenueShare
{

    /**
     * @var array<string,int>
     */
    protected array $allocations = [];

    protected int $remainder;

    public function __construct(array $allocations, int $remainder)
    {
        $this->allocations = $allocations;
        $this->remainder = $remainder;
    }

    public function getAllocationIds(): array
    {
        return array_keys($this->allocations);
    }

    public function getAllocationById(string $id): ?int
    {
        return $this->allocations[$id] ?? null;
    }

    public function getRemainder(): int
    {
        return $this->remainder;
    }
}
