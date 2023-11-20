<?php

namespace App\Services\Finances\Payment\Contracts;

use App\Services\Finances\Objects\OrderInfo;
use App\Services\Mundipagg\Objects\OrderResult;
use App\Subscriber;
use MundiAPILib\Models\CreateOrderRequest;

interface GatewayOrder
{
    /**
     * Creates an order in the gateway
     *
     * @param  \App\Services\Finances\Objects\OrderInfo  $orderInfo
     * @param  \App\Subscriber  $subscriber
     * @param  \MundiAPILib\Models\CreateOrderRequest  $orderRequest
     * @return mixed
     */
    public function createOrder(OrderInfo $orderInfo, Subscriber $subscriber, CreateOrderRequest $orderRequest): OrderResult;
}
