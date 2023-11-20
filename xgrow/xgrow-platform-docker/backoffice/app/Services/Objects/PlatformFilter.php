<?php

namespace App\Services\Objects;

class PlatformFilter extends BaseFilter
{
    public ?string $search = null;
    public ?PeriodFilter $createdPeriod;
    public ?int $clientId = null;
    public ?string $platformId = null;

    public function __construct(
        ?string $search = null,
        ?PeriodFilter $createdPeriod = null,
        ?int $clientId = null,
        ?string $platformId = null
    ) {
        $this->search = $search;
        $this->createdPeriod = $createdPeriod;
        $this->clientId = $clientId;
        $this->platformId = $platformId;
    }

    /**
     * @param  string|null  $search
     * @return PlatformFilter
     */
    public function setSearch(?string $search): PlatformFilter
    {
        $this->search = $search;
        return $this;
    }

    /**
     * @param  int|null  $clientId
     * @return PlatformFilter
     */
    public function setClientId(?int $clientId): PlatformFilter
    {
        $this->clientId = $clientId;
        return $this;
    }


    /**
     * @param string|null $platformId
     * @return PlatformFilter
     */
    public function setPlatformId(?string $platformId): PlatformFilter
    {
        $this->platformId = $platformId;
        return $this;
    }

    /**
     * @param PeriodFilter|null  $createdPeriod
     * @return PlatformFilter
     */
    public function setCreatedPeriod(?PeriodFilter $createdPeriod): PlatformFilter
    {
        $this->createdPeriod = $createdPeriod;
        return $this;
    }

}
