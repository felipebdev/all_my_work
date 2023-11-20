<?php

namespace App\Services\Objects;

class PlanFilter
{
    public ?string $search = null;
    public ?string $platformId = null;
    public ?int $id = null;
    public ?int $productId = null;
    public ?PeriodFilter $createdPeriod;

    public function __construct(
        ?string $search = null,
        ?string $platformId = null,
        ?int $clientId = null,
        ?int $id = null,
        ?int $productId = null,
        ?PeriodFilter $createdPeriod = null
    ) {
        $this->search = $search;
        $this->platformId = $platformId;
        $this->id = $id;
        $this->createdPeriod = $createdPeriod;
    }


    /**
     * @param  int|null  $id
     * @return PlanFilter
     */
    public function setId(?int $id): PlanFilter
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param int|null $productId
     * @return PlanFilter
     */
    public function setProductId(?int $productId): PlanFilter
    {
        $this->productId = $productId;
        return $this;
    }

    /**
     * @param  string|null  $search
     * @return PlanFilter
     */
    public function setSearch(?string $search): PlanFilter
    {
        $this->search = $search;
        return $this;
    }

    /**
     * @param  int|null  $clientId
     * @return PlanFilter
     */
    public function setClientId(?int $clientId): PlanFilter
    {
        $this->clientId = $clientId;
        return $this;
    }

    /**
     * @param  string|null  $platformId
     * @return PlanFilter
     */
    public function setPlatformId(?string $platformId): PlanFilter
    {
        $this->platformId = $platformId;
        return $this;
    }

    /**
     * @param PeriodFilter|null  $createdPeriod
     * @return PlanFilter
     */
    public function setCreatedPeriod(?PeriodFilter $createdPeriod): PlanFilter
    {
        $this->createdPeriod = $createdPeriod;
        return $this;
    }


}
