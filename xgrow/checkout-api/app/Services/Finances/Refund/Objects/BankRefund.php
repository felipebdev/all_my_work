<?php

namespace App\Services\Finances\Refund\Objects;

use App\Services\Finances\Contracts\FromArrayInterface;
use App\Services\Finances\Traits\FromArrayTrait;

final class BankRefund implements FromArrayInterface
{
    use FromArrayTrait;

    final public static function empty(): self
    {
        return new static();
    }

    protected function __construct()
    {
    }

    public string $bankCode;
    public string $agency;
    public ?string $agencyDigit = null;
    public string $account;
    public string $accountDigit;
    public string $accountType;
    public string $documentNumber;
    public string $legalName;
}
