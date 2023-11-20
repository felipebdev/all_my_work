<?php

namespace App\Services\Finances\Split\Calculator\Objects;

#[Immutable]
class XgrowSplitResultWithAnticipation
{
    /**
     * @var int Value after split and transaction taxes
     */
    private int $customerAmount;

    /**
     * @var int Client amount includes anticipation values
     */
    private int $clientAmount;

    /**
     * @var int Includes interests
     */
    private int $serviceAmount;

    /**
     * @var int XGrow amount after customer anticipation
     */
    private int $xgrowAmount;

    /**
     * @var int Taxes (interests not included)
     */
    private int $taxAmount;

    /**
     * SplitResult constructor.
     * @param  int  $customerAmount
     * @param  int  $clientAmount
     * @param  int  $serviceAmount
     * @param  int  $xgrowAmount
     * @param  int  $taxAmount
     */
    public function __construct(
        int $customerAmount,
        int $clientAmount,
        int $serviceAmount,
        int $xgrowAmount,
        int $taxAmount
    ) {
        $this->customerAmount = $customerAmount;
        $this->clientAmount = $clientAmount;
        $this->serviceAmount = $serviceAmount;
        $this->xgrowAmount = $xgrowAmount;
        $this->taxAmount = $taxAmount;
    }

    public function getCustomerAmount(): int
    {
        return $this->customerAmount;
    }

    public function getClientAmount(): int
    {
        return $this->clientAmount;
    }

    public function getServiceAmount(): int
    {
        return $this->serviceAmount;
    }

    public function getXgrowAmount(): int
    {
        return $this->xgrowAmount;
    }

    public function getTaxAmount(): int
    {
        return $this->taxAmount;
    }

}
