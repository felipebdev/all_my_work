<?php

namespace App\Services\Finances\Objects;

use RangeException;

/**
 * Class Coin stores an amount and currency. The amount is stored as an integer, meaning the smallest currency
 * subunit (eg: Brazilian "centavos").
 */
final class Coin
{
    public static $defaultCurrency = 'BRL';

    public static $subunit = 100; // 1 BRL = 100 centavos, 1 USD = 100 cents

    protected int $amount;
    protected string $currency;

    public static function fromInt(int $amount, string $currency = ''): self
    {
        return new static($amount, $currency);
    }

    public static function fromDecimal(float $value, string $currency = ''): self
    {
        return static::fromInt(round($value * self::$subunit), $currency);
    }

    protected function __construct(int $amount, string $currency = '')
    {
        $this->amount = $amount;
        $this->currency = $currency ?: static::$defaultCurrency;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * Get amount in decimal format using the unit of currency (eg: Brazilian Real, US Dollar).
     *
     * @return float
     */
    public function getDecimal(): float
    {
        $subunit ??= self::$subunit;
        return $this->amount / $subunit;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @param  int  $numberOfInstallments
     * @return array<Coin>
     */
    public function allocateInstallments(int $numberOfInstallments): array
    {
        if ($numberOfInstallments <= 0) {
            throw new RangeException('Number must be greater than 0');
        }

        $first = $this->firstInstallment($numberOfInstallments);

        $other = $this->otherInstallments($numberOfInstallments);

        $otherCoins = array_fill(0, $numberOfInstallments - 1, $other);

        return array_merge([$first], $otherCoins);
    }

    public function installmentNumber(int $installmentNumber, int $totalNumberOfInstallments): Coin
    {
        if ($installmentNumber == 1) {
            return $this->firstInstallment($totalNumberOfInstallments);
        }

        return $this->otherInstallments($totalNumberOfInstallments);
    }

    public function firstInstallment(int $numberOfInstallments): Coin
    {
        if ($numberOfInstallments <= 0) {
            throw new RangeException('Number must be greater than 0');
        }

        $otherInstallments = $this->otherInstallments($numberOfInstallments);

        $installmentAmount = $otherInstallments->getAmount();

        $firstInstallmentAmount = $this->getAmount() - ($numberOfInstallments - 1) * $installmentAmount;

        return Coin::fromInt($firstInstallmentAmount, $this->getCurrency());
    }

    public function otherInstallments(int $numberOfInstallments): Coin
    {
        if ($numberOfInstallments <= 0) {
            throw new RangeException('Number must be greater than 0');
        }

        $amount = $this->getAmount();

        $installmentAmount = (int) ($amount / $numberOfInstallments); // truncate values

        return Coin::fromInt($installmentAmount, $this->getCurrency());
    }

}
