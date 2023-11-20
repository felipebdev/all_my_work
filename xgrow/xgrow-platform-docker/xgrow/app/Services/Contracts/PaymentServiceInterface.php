<?php

namespace App\Services\Contracts;

use App\Payment;

interface PaymentServiceInterface {
    public function refund(Payment $payment, bool $single = false);
}
