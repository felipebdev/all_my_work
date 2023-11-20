<?php

namespace App\Services\Mundipagg\Calculator\Objects;

/**
 * Class AmountResult stores values in the smallest currency subunit (eg: Brazilian "centavos")
 *
 * @package App\Services\Mundipagg\Calculator\Objects
 */
#[Immutable]
class AmountResult
{

    public static function create(
        int $planValue,
        int $valueWithDiscounts,
        int $customerValue,
        int $tax,
        int $coupon
    ): self {
        return new self($planValue, $valueWithDiscounts, $customerValue, $tax, $coupon);
    }

    /**
     * @var int Value of each payment
     */
    protected int $value;

    /**
     * @var int Original plan value with discounts
     */
    protected int $valueWithDiscounts;

    /**
     * @var int Customer's value
     */
    protected int $customerValue;

    /**
     * @var int Taxes (including "Transaction tax")
     */
    protected int $tax;

    /**
     * @var int Coupon value
     */
    protected int $coupon;

    public function __construct(
        int $planValue,
        int $valueWithDiscounts,
        int $customerValue,
        int $tax,
        int $coupon
    ) {
        $this->value = $planValue;
        $this->valueWithDiscounts = $valueWithDiscounts;
        $this->customerValue = $customerValue;
        $this->tax = $tax;
        $this->coupon = $coupon;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function getValueWithDiscounts(): int
    {
        return $this->valueWithDiscounts;
    }

    public function getCustomerValue(): int
    {
        return $this->customerValue;
    }

    public function getTax(): int
    {
        return $this->tax;
    }

    public function getCoupon(): int
    {
        return $this->coupon;
    }


}
