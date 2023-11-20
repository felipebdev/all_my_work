<?php

namespace App\Services\Finances\BankAccount\Objects;

use App\Services\Finances\Contracts\FromArrayInterface;
use App\Services\Finances\Traits\FromArrayTrait;

final class BankAccount implements FromArrayInterface
{
    use FromArrayTrait;

    final public static function empty(): self
    {
        return new static();
    }

    public static function fromBankAccountResponse(BankAccountResponse $bankAccountResponse): self
    {
        return self::fromArray([
            'bankCode' => $bankAccountResponse->bankCode,
            'agency' => $bankAccountResponse->agency,
            'agency_digit ' => $bankAccountResponse->agencyDigit,
            'account' => $bankAccountResponse->account,
            'account_digit' => $bankAccountResponse->accountDigit,
            'account_type' => $bankAccountResponse->account,
            'document_number' => $bankAccountResponse->documentNumber,
            'legal_name' => $bankAccountResponse->legalName,
        ]);
    }

    protected function __construct()
    {
    }

    public string $bankCode;
    public string $agency;
    public ?string $agencyDigit = null;
    public string $account;
    public string $accountDigit;
    public string $accountType; // checking/savings
    public string $documentNumber;
    public string $legalName;
}
