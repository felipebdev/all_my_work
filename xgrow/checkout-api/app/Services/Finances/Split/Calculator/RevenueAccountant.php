<?php

namespace App\Services\Finances\Split\Calculator;

use App\Exceptions\InvalidPercentageAllocation;
use App\Services\Finances\Split\Calculator\Objects\RevenueShare;
use RangeException;

/**
 * Class RevenueAccountant is responsible to do a Revenue Share based on given percentages.
 *
 * @package App\Services\Finances\Split\Calculator
 */
class RevenueAccountant
{
    /**
     * @var array<string,float>
     */
    private array $percentages = [];

    /**
     * Add a new stakeholder with percentage participation
     *
     * @param  string  $id
     * @param  float  $percentage
     * @return $this
     */
    public function add(string $id, float $percentage)
    {
        if ($percentage < 0) {
            throw new InvalidPercentageAllocation('Percentage must be positive');
        }

        if ($this->getPercentageAllocated() + $percentage > 100) {
            throw new InvalidPercentageAllocation('Allocation full');
        }

        $this->percentages[$id] = $percentage;
        return $this;
    }

    /**
     * Share the money based on percentages keeping track of remainder money
     *
     * @param  int  $amount
     * @return \App\Services\Finances\Split\Calculator\Objects\RevenueShare
     */
    public function share(int $amount): RevenueShare
    {
        $values = [];
        foreach ($this->getPercentages() as $id => $percentage) {
            $value = (int) round($percentage / 100 * $amount, 0, PHP_ROUND_HALF_DOWN);
            $values[$id] = $value;
        }

        $remainder = $amount - array_sum($values);

        return new RevenueShare($values, $remainder);
    }

    public function getRemainderPercentage(): float
    {
        return 100 - $this->getPercentageAllocated();
    }

    protected function getPercentageAllocated(): float
    {
        return array_sum($this->percentages);
    }

    /**
     * @return array<string,float>
     */
    protected function getPercentages(): array
    {
        return $this->percentages;
    }

}
