<?php

namespace App\Services\Finances\Objects;

use App\Plan;
use RuntimeException;

/**
 * Class useful to simplify management of Order Bumps
 *
 * @package App\Services\Finances\Objects
 */
class OrderBumpsBag
{
    public static function fromRequestData(array $data)
    {
        return new self($data['order_bump'] ?? []);
    }

    public static function empty(): self
    {
        return new self([]);
    }

    private array $orderBumpsPlans = [];

    /**
     * OrderBumpsBag constructor.
     *
     * Try to fill Order Bump Plans if IDs are provided, empty bag if not provided
     *
     * @param  array|null  $orderBumpsIds
     */
    public function __construct(?array $orderBumpsIds = [])
    {
        if ($orderBumpsIds) {
            $this->addOrderBumpsById($orderBumpsIds);
        }
    }

    /**
     * Add order bumps to this bag by id
     *
     * @param  int[]  $orderBumps  Array of order bump IDs
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    protected function addOrderBumpsById(array $orderBumps): self
    {
        foreach ($orderBumps as $orderBumpPlanId) {
            $this->addOrderBump($orderBumpPlanId);
        }
        return $this;
    }

    /**
     * Add single order bump by ID
     *
     * @param $orderBump
     */
    protected function addOrderBump(?int $orderBump): self
    {
        if (is_null($orderBump)) {
            return $this; // do nothing
        }

        if (is_int($orderBump)) {
            $this->orderBumpsPlans[] = Plan::findOrFail($orderBump);
            return $this;
        }

        throw new RuntimeException('Invalid parameter type');
    }

    /**
     * Get all Order Bump Plans
     *
     * @return Plan[] Array with Plan of each order bump
     */
    public function getOrderBumpsPlans(): array
    {
        return $this->orderBumpsPlans ?? [];
    }

    /**
     * Get all Order Bump IDs
     *
     * @return int[] Array with Id of each order bump
     */
    public function getOrderBumpsIds(): array
    {
        $ids = [];
        foreach ($this->orderBumpsPlans as $orderBumpPlan) {
            $ids[] = $orderBumpPlan->id;
        }
        return $ids;
    }

}
