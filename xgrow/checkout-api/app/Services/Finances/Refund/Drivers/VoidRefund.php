<?php

namespace App\Services\Finances\Refund\Drivers;

use App\Services\Finances\Refund\Contracts\RefundInterface;
use App\Services\Finances\Refund\Objects\BankRefund;
use App\Services\Finances\Refund\Objects\PaymentRefund;
use App\Services\Finances\Refund\Objects\RefundResponse;

class VoidRefund implements RefundInterface
{
    public function refund(PaymentRefund $paymentDataRefund, ?BankRefund $bankData = null): RefundResponse
    {
        return RefundResponse::empty();
    }

    public function refundPartial(PaymentRefund $paymentDataRefund, ?BankRefund $bankData = null): RefundResponse
    {
        return RefundResponse::empty();
    }
}
