<?php

namespace App\Services\Objects;

class SubscriberFilter extends BaseFilter
{
    public ?string $search = null;
    public ?PeriodFilter $createdPeriod;
    public ?string $status = null;
    public ?int $clientId = null;
    public ?array $subscribersId = null;
    public ?array $emails = null;
    public ?string $documentNumber = null;

    public function __construct(
        ?string $search = null,
        ?PeriodFilter $createdPeriod = null,
        ?string $status = null,
        ?int $clientId = null,
        ?array $subscribersId = null,
        ?array $emails = null,
        ?string $documentNumber = null

    ) {
        $this->search = $search;
        $this->createdPeriod = $createdPeriod;
        $this->status = $status;
        $this->clientId = $clientId;
        $this->subscribersId = $subscribersId;
        $this->emails = $emails;
        $this->documentNumber = $documentNumber;
    }

    /**
     * @param  string|null  $search
     * @return SubscriberFilter
     */
    public function setSearch(?string $search): SubscriberFilter
    {
        $this->search = $search;
        return $this;
    }

    /**
     * @param  string|null  $status
     * @return SubscriberFilter
     */
    public function setStatus(?string $status): SubscriberFilter
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @param int|null $clientId
     * @return SubscriberFilter
     */
    public function setClientId(?int $clientId): SubscriberFilter
    {
        $this->clientId = $clientId;
        return $this;
    }

    /**
     * @param PeriodFilter|null  $createdPeriod
     * @return SubscriberFilter
     */
    public function setCreatedPeriod(?PeriodFilter $createdPeriod): SubscriberFilter
    {
        $this->createdPeriod = $createdPeriod;
        return $this;
    }

    /**
     * @param array|null $subscribersId
     * @return SubscriberFilter
     */
    public function setSubscribersId(?array $subscribersId): SubscriberFilter
    {
        $this->subscribersId = $subscribersId;
        return $this;
    }

    /**
     * @param array|null $emails
     * @return SubscriberFilter
     */
    public function setEmails(?array $emails): SubscriberFilter
    {
        $this->emails = $emails;
        return $this;
    }

    /**
     * @param string|null $documentNumber
     * @return SubscriberFilter
     */
    public function setDocumentNumber(?string $documentNumber): SubscriberFilter
    {
        $this->documentNumber = $documentNumber;
        return $this;
    }

}
