<?php

namespace App\Services\Pagarme\PagarmeSdkV5;

use PagarmeCoreApiLib\Models\CreateChargeRequest;
use PagarmeCoreApiLib\Models\CreateOrderRequest;
use PagarmeCoreApiLib\Models\GetChargeResponse;
use PagarmeCoreApiLib\Models\GetOrderResponse;
use PagarmeCoreApiLib\Models\UpdateOrderStatusRequest;
use PagarmeCoreApiLib\PagarmeCoreApiClient;

/**
 * Wrapper around Pagarme SDK to simplify some operations
 */
class PagarmeClient
{
    private PagarmeV5Sdk $sdk;

    public function __construct(PagarmeV5Sdk $pagarmeV5Sdk)
    {
        $this->sdk = $pagarmeV5Sdk;
    }

    private function client(): PagarmeCoreApiClient
    {
        return $this->sdk->getClient();
    }

    // Orders

    public function createOrder(CreateOrderRequest $createOrderRequest): GetOrderResponse
    {
        $callback = $this->sdk->getCallbackTemplate('pagarme:create_order');

        $orders = $this->client()->getOrders();
        $orders->setHttpCallBack($callback);

        return $orders->createOrder($createOrderRequest);
    }

    // Charges

    public function createCharge(CreateChargeRequest $createChargeRequest): GetChargeResponse
    {
        $callback = $this->sdk->getCallbackTemplate('pagarme:create_charge');

        $charges = $this->client()->getCharges();
        $charges->setHttpCallBack($callback);

        return $charges->createCharge($createChargeRequest);
    }

    public function captureByChargeId(string $chargeId) : GetChargeResponse{
        $callback = $this->sdk->getCallbackTemplate('pagarme:capture_charge');

        $charges = $this->client()->getCharges();
        $charges->setHttpCallBack($callback);

        return $charges->captureCharge($chargeId);
    }

    public function cancelByChargeId(string $chargeId): GetChargeResponse
    {
        $callback = $this->sdk->getCallbackTemplate('pagarme:cancel_charge');

        $charges = $this->client()->getCharges();
        $charges->setHttpCallBack($callback);

        return $charges->cancelCharge($chargeId);
    }

    // Close order

    public function closeOrderAsPaid(string $id): GetOrderResponse
    {
        return $this->closeOrderWithStatus($id, 'paid');
    }

    public function closeOrderAsCanceled(string $id): GetOrderResponse
    {
        return $this->closeOrderWithStatus($id, 'canceled');
    }

    public function closeOrderAsFailed(string $id): GetOrderResponse
    {
        return $this->closeOrderWithStatus($id, 'failed');
    }

    private function closeOrderWithStatus(string $id, string $status): GetOrderResponse
    {
        $callback = $this->sdk->getCallbackTemplate('pagarme:close_order');

        $orders = $this->client()->getOrders();
        $orders->setHttpCallBack($callback);

        $orderStatus = new UpdateOrderStatusRequest();
        $orderStatus->status = $status;
        return $orders->closeOrder($id, $orderStatus);
    }
}
