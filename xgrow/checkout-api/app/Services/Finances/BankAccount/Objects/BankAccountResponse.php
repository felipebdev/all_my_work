<?php

namespace App\Services\Finances\BankAccount\Objects;

use App\BankInformation;
use App\Services\Finances\Contracts\FromArrayInterface;
use App\Services\Finances\Contracts\SavesRawData;
use App\Services\Finances\Objects\Constants;
use App\Services\Finances\Traits\FromArrayTrait;
use App\Services\Finances\Traits\RawDataTrait;
use JsonSerializable;
use stdClass;

final class BankAccountResponse implements FromArrayInterface, SavesRawData, JsonSerializable
{
    use FromArrayTrait;
    use RawDataTrait;

    public static function fromPagarmeObject(stdClass $object): self
    {
        $responseObject = static::fromArray([
            'bank_code' => $object->bank_code,
            'agency' => $object->agencia,
            'agency_digit' => $object->agencia_dv,
            'account' => $object->conta,
            'account_digit' => $object->conta_dv,
            'account_type' => self::getAccountType($object->type),
            'document_type' => $object->document_type,
            'document_number' => $object->document_number,
            'legal_name' => $object->legal_name,
        ]);

        $responseObject->setRawData($object);

        return $responseObject;
    }

    public static function fromBankInformation(BankInformation $bankInformation): self
    {
        return static::fromArray([
            'bank_code' => $bankInformation->bank,
            'agency' => $bankInformation->branch,
            'agency_digit' => $bankInformation->branch_check_digit,
            'account' => $bankInformation->account,
            'account_digit' => $bankInformation->account_check_digit,
            'account_type' => $bankInformation->account_type,
            'document_type' => $bankInformation->document_type,
            'document_number' => $bankInformation->document,
            'legal_name' => $bankInformation->holder_name,
        ]);
    }

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
    public ?string $accountType = null;
    public ?string $documentType = null; // cnpj/cpf
    public string $documentNumber;
    public string $legalName;

    /**
     * Convert Pagar.me type to internal
     *
     * @return string|null
     */
    public static function getAccountType(?string $type): ?string
    {
        $mapType = [
            Constants::PAGARME_ACCOUNT_TYPE_CHECKING => Constants::XGROW_ACCOUNT_TYPE_CHECKING,
            Constants::PAGARME_ACCOUNT_TYPE_SAVINGS => Constants::XGROW_ACCOUNT_TYPE_SAVINGS,
        ];

        return $mapType[$type] ?? null;
    }

    public function jsonSerialize()
    {
        return [
            'object' => 'bank_account',
            'bank_code' => $this->bankCode,
            'agency' => $this->agency,
            'agency_digit' => $this->agencyDigit,
            'account' => $this->account,
            'account_digit' => $this->accountDigit,
            'account_type' => $this->accountType,
            'document_type' => $this->documentType,
            'document_number' => $this->documentNumber,
            'legal_name' => $this->legalName,
        ];
    }
}
