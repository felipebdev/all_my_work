<?php

namespace App\Services\Finances\Refund\Objects;

use App\Payment;

final class Refunded
{
    public Payment $payment;
    public RefundResponse $refundResponse;

    public static function create(Payment $payment, RefundResponse $refundResponse): self
    {
        return new self($payment, $refundResponse);
    }

    public function __construct(Payment $payment, RefundResponse $refundResponse)
    {
        $this->payment = $payment;
        $this->refundResponse = $refundResponse;
    }

}
