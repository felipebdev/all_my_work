<?php

namespace App\Services\Finances\Refund\Objects;

use App\Services\Finances\Contracts\FromArrayInterface;
use App\Services\Finances\Traits\FromArrayTrait;

final class RefundOptions implements FromArrayInterface
{
    use FromArrayTrait;

    public static function default(): self
    {
        return new static();
    }

    public bool $refundAll = true;
}
