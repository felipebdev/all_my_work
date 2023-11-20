<?php

namespace App\Services\Finances\Transfer\Objects;

use App\Services\Finances\Contracts\FromArrayInterface;
use App\Services\Finances\Traits\FromArrayTrait;
use Carbon\CarbonImmutable;
use DateTimeInterface;

final class TransferFilter implements FromArrayInterface
{
    use FromArrayTrait;

    public static $dateFormat = 'U'; // Unix timestamp (seconds)

    final public static function empty(): self
    {
        return new static();
    }

    public ?string $count = null;
    public ?string $page = null;
    public ?string $bankAccountId = null;
    public ?string $amount = null;
    public ?string $recipientId = null;
    public ?DateTimeInterface $createdAfter = null;
    public ?DateTimeInterface $createdBefore = null;

    protected function __construct()
    {
    }

    protected function withCreatedAfter($value): self
    {
        $date = CarbonImmutable::createFromFormat(self::$dateFormat, $value)->setMilliseconds(0);
        $this->createdAfter = $date;
        return $this;
    }

    protected function withCreatedBefore($value): self
    {
        $date = CarbonImmutable::createFromFormat(self::$dateFormat, $value)->setMilliseconds(999);
        $this->createdBefore = $date;
        return $this;
    }

}
