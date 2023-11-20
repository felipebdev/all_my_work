<?php

namespace App\Services\Finances\Refund\Contracts;

use App\Services\Finances\Refund\Objects\BankRefund;
use App\Services\Finances\Refund\Objects\PaymentRefund;
use App\Services\Finances\Refund\Objects\RefundResponse;

interface RefundInterface
{
    /**
     * @param  \App\Services\Finances\Refund\Objects\PaymentRefund  $paymentDataRefund
     * @param  \App\Services\Finances\Refund\Objects\BankRefund|null  $bankData
     * @return \App\Services\Finances\Refund\Objects\RefundResponse
     * @throws \App\Exceptions\Finances\RefundFailedException
     * @throws \App\Exceptions\Finances\TransactionNotFoundException
     */
    public function refund(PaymentRefund $paymentDataRefund, ?BankRefund $bankData = null): RefundResponse;

    /**
     * Partial refund uses payment_plan_id
     *
     * @param  \App\Services\Finances\Refund\Objects\PaymentRefund  $paymentDataRefund
     * @param  \App\Services\Finances\Refund\Objects\BankRefund|null  $bankData
     * @return \App\Services\Finances\Refund\Objects\RefundResponse
     */
    public function refundPartial(PaymentRefund $paymentDataRefund, ?BankRefund $bankData = null): RefundResponse;
}
