<?php

namespace App\Services\Finances\Payment\Contracts;

use App\Payment;
use App\Services\Finances\Objects\OrderInfo;
use App\Services\Finances\Objects\PaymentOrderResult;
use App\Services\Mundipagg\Objects\OrderResult;
use App\Subscriber;

interface PaymentMethodOrder
{
    /**
     * Execute an order request
     *
     * @param  \App\Services\Finances\Objects\OrderInfo  $orderInfo
     * @param  \App\Subscriber  $subscriber
     * @throw  \App\Services\Finances\Payment\Exceptions\FailedTransaction
     * @return mixed
     */
    public function order(OrderInfo $orderInfo, Subscriber $subscriber): OrderResult;

    /**
     * Actions to be executed after order process (eg: send "boleto")
     *
     * @param  \MundiAPILib\Models\GetOrderResponse|\PagarmeCoreApiLib\Models\GetOrderResponse  $orderResponse
     * @param  \App\Subscriber  $subscriber
     * @param  \App\Payment  ...$payments
     * @return \App\Services\Finances\Objects\PaymentOrderResult
     */
    public function confirmOrder(
        $orderResponse, // @todo Create wrapper object
        Subscriber $subscriber,
        Payment ...$payments
    ): PaymentOrderResult;

}
