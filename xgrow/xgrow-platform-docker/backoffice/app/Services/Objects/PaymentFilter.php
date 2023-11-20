<?php

namespace App\Services\Objects;

class PaymentFilter extends BaseFilter
{

    public ?PeriodFilter $paymentDate;
    public ?string $status = null;
    public ?int $clientId = null;
    public ?int $subscriberId = null;

    public function __construct(
        ?PeriodFilter $paymentDate = null,
        ?string $status = null,
        ?int $clientId = null,
        ?int $subscriberId = null

    ) {
        $this->paymentDate = $paymentDate;
        $this->status = $status;
        $this->clientId = $clientId;
        $this->subscriberId = $subscriberId;
    }

    /**
     * @param  string|null  $status
     * @return PaymentFilter
     */
    public function setStatus(?string $status): PaymentFilter
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @param int|null $clientId
     * @return PaymentFilter
     */
    public function setClientId(?int $clientId): PaymentFilter
    {
        $this->clientId = $clientId;
        return $this;
    }

    /**
     * @param int|null $subscriberId
     * @return PaymentFilter
     */
    public function setSubscriberId(?int $subscriberId): PaymentFilter
    {
        $this->clientId = $subscriberId;
        return $this;
    }

    /**
     * @param PeriodFilter|null  $paymentDate
     * @return PaymentFilter
     */
    public function setPaymentDate(?PeriodFilter $paymentDate): PaymentFilter
    {
        $this->paymentDate = $paymentDate;
        return $this;
    }

}
