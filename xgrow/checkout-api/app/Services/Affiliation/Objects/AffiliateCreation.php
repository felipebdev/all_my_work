<?php

namespace App\Services\Affiliation\Objects;

use App\Services\Finances\Contracts\FromArrayInterface;
use App\Services\Finances\Traits\FromArrayTrait;

final class AffiliateCreation implements FromArrayInterface
{
    use FromArrayTrait;

    public string $name;
    public string $email;
    public string $documentType;
    public string $documentNumber;
    public string $legalName;
    public string $accountType;
    public string $bankCode;
    public string $agency;
    public ?string $agencyDigit = null;
    public string $account;
    public string $accountDigit;

    final public static function empty(): self
    {
        return new static();
    }

    protected function __construct()
    {
    }
}
