<?php

namespace App\Services\Finances\Refund\Objects;

use App\Services\Finances\Contracts\FromArrayInterface;
use App\Services\Finances\Contracts\SavesRawData;
use App\Services\Finances\Traits\FromArrayTrait;
use App\Services\Finances\Traits\RawDataTrait;
use Carbon\Carbon;
use DateTimeInterface;
use JsonSerializable;
use stdClass;

final class RefundResponse implements FromArrayInterface, SavesRawData, JsonSerializable
{

    use FromArrayTrait;
    use RawDataTrait;

    public static function fromPagarmeObject(stdClass $object): self
    {
        return static::fromArray((array) $object);
    }

    public static function empty(): self
    {
        return new static();
    }

    /**
     * Protected constructor prevents direct instantiation, use named constructor instead
     * @see \App\Services\Finances\Transfer\Objects\TransferResponse::fromPagarmeObject()
     */
    protected function __construct()
    {
    }

    protected ?string $object = null;
    protected ?int $id = null;
    protected ?int $amount = null;

    protected ?string $type = null;
    protected ?string $status = null;

    protected ?string $transactionId = null;
    protected ?DateTimeInterface $createdAt = null;
    protected array $metadata = [];
    protected bool $isPartial = false;

    protected function withDateCreated($value): self
    {
        $this->createdAt = Carbon::parse($value);
        return $this;
    }

    protected function withMetadata($value): self
    {
        $this->metadata = (array) $value;
        return $this;
    }

    public function isPartial(): bool
    {
        return $this->isPartial;
    }

    public function setIsPartial(bool $isPartial = true): RefundResponse
    {
        $this->isPartial = $isPartial;
        return $this;
    }

    public function getObject(): ?string
    {
        return $this->object;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function getTransactionId(): ?string
    {
        return $this->transactionId;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function jsonSerialize()
    {
        return [
            'object' => (string) $this->object,
            'id' => $this->id,
            'amount' => $this->amount,
            'is_partial' => $this->isPartial,
            'type' => (string) $this->type,
            'status' => (string) $this->status,
            'transaction_id' => $this->transactionId,
            'created_at' => (string) $this->createdAt ? $this->createdAt->format(DateTimeInterface::ISO8601) : null,
            'metadata' => $this->metadata,
        ];
    }
}
