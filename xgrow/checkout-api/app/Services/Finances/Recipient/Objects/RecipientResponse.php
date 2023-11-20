<?php

namespace App\Services\Finances\Recipient\Objects;

use App\BankInformation;
use App\Services\Finances\Contracts\FromArrayInterface;
use App\Services\Finances\Contracts\SavesRawData;
use App\Services\Finances\Objects\Constants;
use App\Services\Finances\Traits\FromArrayTrait;
use App\Services\Finances\Traits\RawDataTrait;
use Carbon\Carbon;
use DateTimeInterface;
use JsonSerializable;
use stdClass;

final class RecipientResponse implements FromArrayInterface, SavesRawData, JsonSerializable
{

    use FromArrayTrait;
    use RawDataTrait;

    private static $recipientCanTransact = [
        Constants::PAGARME_RECIPIENT_STATUS_REGISTRATION => true,
        Constants::PAGARME_RECIPIENT_STATUS_AFFILIATION => true,
        Constants::PAGARME_RECIPIENT_STATUS_ACTIVE => true,
        //
        Constants::PAGARME_RECIPIENT_STATUS_REFUSED => false,
        Constants::PAGARME_RECIPIENT_STATUS_SUSPENDED => false,
        Constants::PAGARME_RECIPIENT_STATUS_BLOCKED => false,
        Constants::PAGARME_RECIPIENT_STATUS_INACTIVE => false,
    ];

    public static function fromPagarmeRaw(stdClass $object): self
    {
        $object->canTransact = self::$recipientCanTransact[$object->status] ?? null;

        return static::fromArray((array) $object);
    }

    public static function fromBankInformation(BankInformation $bankInformation): self
    {
        return static::fromArray([
            'id' => $bankInformation->recipient_id,
            'name' => $bankInformation->holder_name,
            'email' => $bankInformation->email,
            'type' => $bankInformation->account_type,
            'status' => $bankInformation->recipient_status,
            'reason' => $bankInformation->recipient_reason,
            'can_transact' => self::$recipientCanTransact[$bankInformation->recipient_status] ?? null,
            'created_at' => Carbon::now(),
        ]);
    }

    public static function empty(): self
    {
        return new static();
    }

    protected function __construct()
    {
    }

    protected string $object = 'recipient';
    protected string $id;
    protected string $name;
    protected string $email;
    protected string $type;
    protected ?string $status = null;
    protected ?string $reason = null;
    protected ?bool $canTransact = null;
    protected ?DateTimeInterface $createdAt = null;

    protected function withCreatedAt($value): self
    {
        $this->createdAt = Carbon::parse($value);
        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function canTransact(): ?bool
    {
        return $this->canTransact;
    }

    public function jsonSerialize()
    {
        return [
            'object' => $this->object,
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'type' => $this->type,
            'status' => $this->status ?? null,
            'reason' => $this->reason ?? null,
            'can_transact' => $this->canTransact ?? null,
        ];
    }
}
