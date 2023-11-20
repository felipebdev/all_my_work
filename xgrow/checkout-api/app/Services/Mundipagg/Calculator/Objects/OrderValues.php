<?php

namespace App\Services\Mundipagg\Calculator\Objects;

#[Immutable]
class OrderValues
{

    public static $subunit = 100;

    /**
     * Create a new instance from decimal values (eg: 12.34 for R$12,34)
     */
    public static function fromDecimal(
        float $price,
        float $coupon,
        float $percentSplit,
        float $clientTaxTransaction
    ): self {
        return new self($price * self::$subunit, $coupon * self::$subunit, $percentSplit, $clientTaxTransaction * self::$subunit);
    }

    public static function create(int $price, int $coupon, float $percentSplit, int $clientTaxTransaction): self
    {
        return new self($price, $coupon, $percentSplit, $clientTaxTransaction);
    }

    /**
     * @var int Original price
     */
    public int $price;

    /**
     * @var int Coupon value
     */
    public int $coupon;

    /**
     * @var float Customer percentage share as usually expressed (eg: 95.0 for 95%)
     */
    public float $percentSplit;

    /**
     * @var int Client tax transaction
     */
    public int $clientTaxTransaction;

    /**
     * OrderValues constructor.
     * @param  int  $price
     * @param  int  $coupon
     * @param  float  $percentSplit
     * @param  int  $clientTaxTransaction
     */
    public function __construct(int $price, int $coupon, float $percentSplit, int $clientTaxTransaction)
    {
        $this->price = $price;
        $this->coupon = $coupon;
        $this->percentSplit = $percentSplit;
        $this->clientTaxTransaction = $clientTaxTransaction;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function getCoupon(): int
    {
        return $this->coupon;
    }

    public function getPercentSplit(): float
    {
        return $this->percentSplit;
    }

    public function getClientTaxTransaction(): int
    {
        return $this->clientTaxTransaction;
    }


}
