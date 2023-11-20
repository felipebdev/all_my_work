<?php


namespace App\Services\Finances\Objects;


class PaymentOrderResult
{
    public bool $isSuccessful;
    public bool $isPendingPayment;

    /**
     * PaymentOrderResult constructor.
     *
     * @param  bool  $isSuccessful  True if payment process ran accordingly expectations, false otherwise
     * @param  bool  $isPendingPayment  True if process ran accordingly but payment is not confirmed yet (eg: boleto)
     */
    public function __construct(bool $isSuccessful, bool $isPendingPayment)
    {
        $this->isSuccessful = $isSuccessful;
        $this->isPendingPayment = $isPendingPayment;
    }

}
