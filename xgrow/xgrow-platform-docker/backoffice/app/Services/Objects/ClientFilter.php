<?php

namespace App\Services\Objects;

class ClientFilter extends BaseFilter
{
    public ?string $search = null;
    public ?string $clientType = null;
    public ?array $clientsId = null;
    public ?PeriodFilter $createdPeriod;

    public function __construct(
        ?string $search = null,
        ?string $clientType = null,
        ?array $clientsId = null,
        ?PeriodFilter $createdPeriod = null
    ) {
        $this->search = $search;
        $this->clientsId = $clientsId;
        $this->createdPeriod = $createdPeriod;
        $this->clientType = $clientType;
    }

    /**
     * @param  string|null  $search
     * @return ClientFilter
     */
    public function setSearch(?string $search): ClientFilter
    {
        $this->search = $search;
        return $this;
    }

    /**
     * @param  array|null  $clientsId
     * @return ClientFilter
     */
    public function setClientsId(?array $clientsId): ClientFilter
    {
        $this->clientsId = $clientsId;
        return $this;
    }

    /**
     * @param PeriodFilter|null  $createdPeriod
     * @return ClientFilter
     */
    public function setCreatedPeriod(?PeriodFilter $createdPeriod): ClientFilter
    {
        $this->createdPeriod = $createdPeriod;
        return $this;
    }

    public function setClientType(?string $clientType): ClientFilter
    {
        $this->clientType = $clientType;
        return $this;
    }
}
