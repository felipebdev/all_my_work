<?php


namespace App\Services\Mundipagg\Calculator\Objects;

#[Immutable]
class DistributionResult
{
    public static $subunit = 100; // subunit "centavos" by default

    public int $originalValue;
    public int $valueWithDiscounts;
    public int $valueWithInterests;
    public int $customerValue;
    public int $tax;
    public int $coupon;

    /**
     * DistributionResult constructor.
     * @param  int  $value
     * @param  int  $valueWithDiscounts
     * @param  int  $valueWithInterests
     * @param  int  $customerValue
     * @param  int  $tax
     * @param  int  $coupon
     */
    public function __construct(
        int $value,
        int $valueWithDiscounts,
        int $valueWithInterests,
        int $customerValue,
        int $tax,
        int $coupon
    ) {
        $this->originalValue = $value;
        $this->valueWithDiscounts = $valueWithDiscounts;
        $this->valueWithInterests = $valueWithInterests;
        $this->customerValue = $customerValue;
        $this->tax = $tax;
        $this->coupon = $coupon;
    }

    public function getOriginalValue(): int
    {
        return $this->originalValue;
    }

    public function getValueWithDiscounts(): int
    {
        return $this->valueWithDiscounts;
    }

    public function getValueWithInterests(): int
    {
        return $this->valueWithInterests;
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

    public function getDecimalOriginalValue(): float
    {
        return $this->toDecimal($this->originalValue);
    }

    public function getDecimalValueWithDiscounts(): float
    {
        return $this->toDecimal($this->valueWithDiscounts);
    }

    public function getDecimalValueWithInterests(): float
    {
        return $this->toDecimal($this->valueWithInterests);
    }

    public function getDecimalCustomerValue(): float
    {
        return $this->toDecimal($this->customerValue);
    }

    public function getDecimalTax(): float
    {
        return $this->toDecimal($this->tax);
    }

    public function getDecimalCoupon(): float
    {
        return $this->toDecimal($this->coupon);
    }

    private function toDecimal(int $value, ?int $subunit = null): float
    {
        return $value / ($subunit ?? self::$subunit);
    }

    public function join(self $distributionResult): self
    {
        return new self(
            $this->getOriginalValue() + $distributionResult->getOriginalValue(),
            $this->getValueWithDiscounts() + $distributionResult->getValueWithDiscounts(),
            $this->getValueWithInterests() + $distributionResult->getValueWithInterests(),
            $this->getCustomerValue() + $distributionResult->getCustomerValue(),
            $this->getTax() + $distributionResult->getTax(),
            $this->getCoupon() + $distributionResult->getCoupon()
        );
    }


}
