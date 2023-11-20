<?php

namespace App\Services\Objects;

class PlatformUserFilter extends BaseFilter
{
    public ?string $search = null;

    public ?string $status = null;

    public ?PeriodFilter $createdPeriod;
    public ?int $platformId = null;

    public function __construct(
        ?string $search = null,
        ?string $status = null,
        ?PeriodFilter $createdPeriod = null,
        ?int $platformId = null
    ) {
        $this->search = $search;
        $this->status = $status;
        $this->createdPeriod = $createdPeriod;
        $this->platformId = $platformId;
    }

    /**
     * @param  string|null  $search
     * @return PlatformUserFilter
     */
    public function setSearch(?string $search): PlatformUserFilter
    {
        $this->search = $search;
        return $this;
    }

    /**
     * @param string|null $status
     * @return PlatformUserFilter
     */
    public function setStatus(?string $status): PlatformUserFilter
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @param int|null $platformId
     * @return PlatformUserFilter
     */
    public function setPlatformId(?int $platformId): PlatformUserFilter
    {
        $this->platformId = $platformId;
        return $this;
    }

    /**
     * @param PeriodFilter|null  $createdPeriod
     * @return PlatformUserFilter
     */
    public function setCreatedPeriod(?PeriodFilter $createdPeriod): PlatformUserFilter
    {
        $this->createdPeriod = $createdPeriod;
        return $this;
    }

}
