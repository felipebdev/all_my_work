<?php


namespace App\Services\Finances\Objects;

use App\Services\Finances\Payment\Exceptions\InvalidOrderException;
use App\Services\Objects\FillableObject;

/**
 * PaymentInfo is a generic payment method
 *
 * @package App\Services\Finances\Objects
 */
class PaymentInfo extends FillableObject
{

    public static function fromPaymentInfo(array $data): self
    {
        $paymentInfo = (new self())
            ->withInstallment($data['installment'] ?? 1)
            ->withValue($data['value'] ?? '0');

        if ($data['currency'] ?? null) {
            $paymentInfo->withCurrencyCode($data['currency']);
        }

        return $paymentInfo;
    }

    public int $installment;

    /**
     * Value as string in
     */
    public string $value;

    /**
     * Three letter currency code, as defined in ISO 4217
     */
    public string $currencyCode = 'BRL';

    /**
     * This class must not be instantiated directly, use static factory instead.
     */
    protected function __construct(?array $data = [])
    {
        parent::__construct($data);
    }

    public function getInstallment(): int
    {
        return $this->installment;
    }

    protected function withInstallment(int $installment): self
    {
        $this->installment = $installment;
        return $this;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    protected function withValue(string $value): self
    {
        $this->value = $value;
        return $this;
    }

    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    protected function withCurrencyCode(string $currencyCode): self
    {
        if (mb_strlen($currencyCode) !== 3) {
            throw new InvalidOrderException("Invalid currency code (provided: {$currencyCode})");
        }

        $this->currencyCode = strtoupper($currencyCode);
        return $this;
    }

}
