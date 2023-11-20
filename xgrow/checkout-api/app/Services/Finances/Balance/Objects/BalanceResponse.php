<?php

namespace App\Services\Finances\Balance\Objects;

use App\Services\Finances\Contracts\SavesRawData;
use App\Services\Finances\Traits\RawDataTrait;
use JsonSerializable;
use MundiAPILib\Models\GetBalanceResponse;
use stdClass;

final class BalanceResponse implements SavesRawData, JsonSerializable
{

    use RawDataTrait;

    public static function fromPagarmeObject(stdClass $object): self
    {
        $current = $object->available->amount ?? 0;
        $pending = $object->waiting_funds->amount ?? 0;

        $available = $pending < 0 ? $current + $pending : $current;
        $pending = $pending < 0 ? $current + $pending : $current;

        $self = new self();
        $self->current = $current;
        $self->available = $available;
        $self->pending = $pending;
        $self->transferred = $object->transferred->amount ?? 0;
        $self->rawData = $object;

        return $self;
    }

    public static function fromMundipaggObject(GetBalanceResponse $object): self
    {
        $current = $object->availableAmount ?? 0;
        $pending = $object->waitingFundsAmount ?? 0;

        $available = $pending < 0 ? $current + $pending : $current;
        $pending = $pending < 0 ? $current + $pending : $pending;

        $self = new self();
        $self->current = $current;
        $self->available = $available;
        $self->pending = $pending;
        $self->transferred = $object->transferredAmount ?? 0;
        $self->rawData = $object;

        return $self;
    }

    public static function empty(): self
    {
        return new static();
    }

    protected function __construct()
    {
    }

    protected string $object = 'balance';
    protected int $current = 0;
    protected int $available = 0;
    protected int $pending = 0;
    protected int $transferred = 0;
    protected int $anticipation = 0;

    /**
     * Create a new instance including anticipation in calculations
     *
     * @param  int  $anticipationAmount  Anticipation amount (in "centavos")
     * @return $this New instance
     */
    public function cloneWithAnticipation(int $anticipationAmount): self
    {
        $self = clone $this;
        $self->anticipation = $anticipationAmount;
        $self->pending = $this->pending - $anticipationAmount;
        return $self;
    }

    public function jsonSerialize()
    {
        return [
            'object' => (string) $this->object,
            'current' => $this->current, // current balance
            'available' => $this->available, // available to transfer (aka, withdrawal)
            'pending' => $this->pending, // not available yeg (eg: rolling pay-cycle)
            'transferred' => $this->transferred, // already transferred
            'anticipation' => $this->anticipation, // anticipation amount
        ];
    }
}
