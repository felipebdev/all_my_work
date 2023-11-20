<?php

namespace App\Services\Finances\Split\Calculator\Objects;

#[Immutable]
class XgrowSplitResult
{
    /**
     * @var int Value after split and transaction taxes
     */
    private int $customerAmount;

    /**
     * @var int Includes interests
     */
    private int $serviceAmount;

    /**
     * @var int Taxes (interests not included)
     */
    private int $taxAmount;

    /**
     * @var int Total transaction tax (fixed tax)
     */
    private int $transactionTax;

    /**
     * SplitResult constructor.
     * @param  int  $customerAmount
     * @param  int  $serviceAmount
     * @param  int  $taxAmount
     * @param  int  $transactionTax
     */
    public function __construct(
        int $customerAmount,
        int $serviceAmount,
        int $taxAmount,
        int $transactionTax
    ) {
        $this->customerAmount = $customerAmount;
        $this->serviceAmount = $serviceAmount;
        $this->taxAmount = $taxAmount;
        $this->transactionTax = $transactionTax;
    }

    public function getCustomerAmount(): int
    {
        return $this->customerAmount;
    }

    public function getServiceAmount(): int
    {
        return $this->serviceAmount;
    }

    public function getTaxAmount(): int
    {
        return $this->taxAmount;
    }

    public function getTransactionTax(): int
    {
        return $this->transactionTax;
    }

}
