<?php


namespace App\Services\Finances\Payment\Contracts;

/**
 * Interface PaymentMethodCancelable
 *
 * Implement this interface if a payment method has an option to cancel (eg: credit card, registered "boleto")
 *
 * @package App\Services\Finances\Payment\Contracts
 */
interface PaymentMethodCancelable
{
    public function cancelCharge($chargeId): bool;
}
