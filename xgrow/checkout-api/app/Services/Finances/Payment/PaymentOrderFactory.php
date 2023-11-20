<?php

namespace App\Services\Finances\Payment;

use App\Services\Finances\Objects\Constants;
use App\Services\Finances\Payment\Contracts\PaymentMethodOrder;
use App\Services\Finances\Payment\Exceptions\InvalidOrderException;
use App\Services\Finances\Payment\Strategies\BoletoOrder;
use App\Services\Finances\Payment\Strategies\CreditCardOrder;
use App\Services\Finances\Payment\Strategies\MultiMeansOrder;
use App\Services\Finances\Payment\Strategies\PixOrder;

/**
 * This class helps to produce a proper strategy object for handling a payment.
 */
class PaymentOrderFactory
{
    /**
     * @param  string  $paymentMethodName
     * @return \App\Services\Finances\Payment\Contracts\PaymentMethodOrder
     * @throws \Exception
     */
    public static function getPaymentMethod(string $paymentMethodName): PaymentMethodOrder
    {
        switch ($paymentMethodName) {
            case Constants::XGROW_PIX:
                return resolve(PixOrder::class);
            case Constants::XGROW_BOLETO:
                return resolve(BoletoOrder::class);
            case Constants::XGROW_CREDIT_CARD:
                return resolve(CreditCardOrder::class);
            case Constants::XGROW_MULTIMEANS:
                return resolve(MultiMeansOrder::class);
            default:
                throw new InvalidOrderException('Unknown Payment Method');
        }
    }
}
