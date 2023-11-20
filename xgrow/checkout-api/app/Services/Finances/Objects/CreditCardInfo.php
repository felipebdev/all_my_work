<?php

namespace App\Services\Finances\Objects;

class CreditCardInfo extends PaymentInfo
{

    public static function fromCcInfo(array $data): self
    {
        $creditCardInfo = (new self())
            ->withInstallment($data['installment'] ?? 0)
            ->withValue($data['value'] ?? '0');

        if ($data['currency'] ?? null) {
            $creditCardInfo->withCurrencyCode($data['currency']);
        }

        $creditCardInfo->withTokenId($data['token'] ?? '')
            ->withNumber($data['number'] ?? '')
            ->withHolderName($data['holder_name'] ?? '')
            ->withHolderDocument($data['holder_document'] ?? '')
            ->withExpMonth($data['exp_month'] ?? '')
            ->withExpYear($data['exp_year'] ?? '')
            ->withBrand($data['brand'] ?? '')
            ->withCvv($data['cvv'] ?? '');

        return $creditCardInfo;
    }

    protected ?string $tokenId;
    protected ?string $number;
    protected ?string $holderName;
    protected ?string $holderDocument;
    protected ?string $expMonth;
    protected ?string $expYear;

    /**
     * @var string|null Elo, Mastercard, Visa, Amex, JCB, Aura, Hipercard, Diners, Discover, etc
     */
    protected ?string $brand;
    protected ?string $cvv;

    /**
     * This class must not be instantiated directly, use static factory instead.
     */
    protected function __construct()
    {
        parent::__construct();
    }

    public function getTokenId(): string
    {
        return $this->tokenId ?? '';
    }

    protected function withTokenId(?string $tokenId): self
    {
        $this->tokenId = $tokenId;
        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    protected function withNumber(?string $number): self
    {
        $this->number = $number;
        return $this;
    }

    public function getHolderName(): ?string
    {
        return $this->holderName;
    }

    protected function withHolderName(?string $holderName): self
    {
        $this->holderName = $holderName;
        return $this;
    }

    public function getHolderDocument(): ?string
    {
        return $this->holderDocument;
    }

    protected function withHolderDocument(?string $holderDocument): self
    {
        $this->holderDocument = $holderDocument;
        return $this;
    }

    public function getExpMonth(): ?string
    {
        return $this->expMonth;
    }

    protected function withExpMonth(?string $expMonth): self
    {
        $this->expMonth = $expMonth;
        return $this;
    }

    public function getExpYear(): ?string
    {
        return $this->expYear;
    }

    protected function withExpYear(?string $expYear): self
    {
        $this->expYear = $expYear;
        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    protected function withBrand(?string $brand): self
    {
        $this->brand = $brand;
        return $this;
    }

    public function getCvv(): ?string
    {
        return $this->cvv;
    }

    protected function withCvv(?string $cvv): self
    {
        $this->cvv = $cvv;
        return $this;
    }

}
