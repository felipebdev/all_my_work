<?php

namespace App\Services\Objects;

class ProductFilter
{
    public ?string $search = null;
    public ?string $analysisStatus = null;
    public ?string $platformId = null;
    public ?int $clientId = null;
    public ?int $id = null;
    public ?PeriodFilter $createdPeriod;

    public function __construct(
        ?string $search = null,
        ?string $analysisStatus = null,
        ?string $platformId = null,
        ?int $clientId = null,
        ?int $id = null,
        ?PeriodFilter $createdPeriod = null
    ) {
        $this->search = $search;
        $this->analysisStatus = $analysisStatus;
        $this->platformId = $platformId;
        $this->clientId = $clientId;
        $this->id = $id;
        $this->createdPeriod = $createdPeriod;
    }

    /**
     * @param  int|null  $id
     * @return ProductFilter
     */
    public function setId(?int $id): ProductFilter
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param  string|null  $search
     * @return ProductFilter
     */
    public function setSearch(?string $search): ProductFilter
    {
        $this->search = $search;
        return $this;
    }

    /**
     * @param  string|null  $analysisStatus
     * @return ProductFilter
     */
    public function setAnalysisStatus(?string $analysisStatus): ProductFilter
    {
        $this->analysisStatus = $analysisStatus;
        return $this;
    }

    /**
     * @param  int|null  $clientId
     * @return ProductFilter
     */
    public function setClientId(?int $clientId): ProductFilter
    {
        $this->clientId = $clientId;
        return $this;
    }

    /**
     * @param  string|null  $platformId
     * @return ProductFilter
     */
    public function setPlatformId(?string $platformId): ProductFilter
    {
        $this->platformId = $platformId;
        return $this;
    }

    /**
     * @param PeriodFilter|null  $createdPeriod
     * @return ProductFilter
     */
    public function setCreatedPeriod(?PeriodFilter $createdPeriod): ProductFilter
    {
        $this->createdPeriod = $createdPeriod;
        return $this;
    }

}
