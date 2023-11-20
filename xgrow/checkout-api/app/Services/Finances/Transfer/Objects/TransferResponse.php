<?php

namespace App\Services\Finances\Transfer\Objects;

use App\Services\Finances\Contracts\FromArrayInterface;
use App\Services\Finances\Contracts\SavesRawData;
use App\Services\Finances\Traits\FromArrayTrait;
use App\Services\Finances\Traits\RawDataTrait;
use App\Services\Finances\Transfer\Mappings\PagarmeTransferObjectMap;
use Carbon\Carbon;
use DateTimeInterface;
use JsonSerializable;
use stdClass;

final class TransferResponse implements FromArrayInterface, SavesRawData, JsonSerializable
{

    use FromArrayTrait;
    use RawDataTrait;

    public const STATUS_PENDING = 'pending';
    public const STATUS_TRANSFERRED = 'transferred';
    public const STATUS_FAILED = 'failed';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_CANCELED = 'canceled';

    public static function fromPagarmeObject(stdClass $object): self
    {
        $data = (array) $object;
        $data['status'] = PagarmeTransferObjectMap::status($object->status);
        return static::fromArray($data);
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
    protected ?string $status = null;
    protected ?string $sourceId = null;
    protected ?string $targetId = null;
    protected ?string $transactionId = null;
    protected ?DateTimeInterface $createdAt = null;
    protected array $metadata = [];

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function getSourceId(): ?string
    {
        return $this->sourceId;
    }

    public function getTargetId(): ?string
    {
        return $this->targetId;
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
            'status' => $this->status,
            'source_id' => (string) $this->sourceId,
            'target_id' => (string) $this->targetId,
            'transaction_id' => $this->transactionId,
            'created_at' => (string) $this->createdAt ? $this->createdAt->format(DateTimeInterface::ISO8601) : null,
            'metadata' => $this->metadata,
        ];
    }
}
