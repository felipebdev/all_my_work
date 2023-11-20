<?php

namespace App\Services\Finances\Payment;

use App\Exceptions\CaptureFailedException;
use App\Payment;
use App\Plan;
use App\Services\Contracts\PlatformableInterface;
use App\Services\Finances\Objects\AddressInfo;
use App\Services\Finances\Objects\Constants;
use App\Services\Finances\Objects\OrderInfo;
use App\Services\Finances\Payment\Contracts\PaymentMethodCancelable;
use App\Services\Finances\Product\ProductInformationService;
use App\Services\MundipaggService;
use App\Services\Traits\PlatformableTrait;
use App\Transaction;
use Illuminate\Support\Facades\Log;
use MundiAPILib\APIException;
use MundiAPILib\Http\HttpCallBack;
use MundiAPILib\Http\HttpContext;
use MundiAPILib\Models\CreateAddressRequest;
use MundiAPILib\Models\CreateCardRequest;
use MundiAPILib\Models\CreateCreditCardPaymentRequest;
use MundiAPILib\Models\CreateOrderItemRequest;
use MundiAPILib\Models\CreateOrderRequest;
use MundiAPILib\Models\CreatePaymentRequest;
use MundiAPILib\Models\GetOrderResponse;
use MundiAPILib\Models\UpdateOrderStatusRequest;

class PaymentCreditCardService implements PlatformableInterface, PaymentMethodCancelable
{
    use PlatformableTrait;

    protected ProductInformationService $productInformationService;

    public function __construct(?string $platformId = null)
    {
        $this->productInformationService = new ProductInformationService();

        if ($platformId) {
            $this->setPlatformId($platformId);
        }
    }

    public function createTestOrder(OrderInfo $orderInfo, $cardId, CreateOrderRequest $request): GetOrderResponse
    {
        $plan = $orderInfo->finder->rememberPlan();

        //Set metadata
        $metadata = $this->productInformationService->getOrderMetadata($plan, $orderInfo);
        $metadata['obs'] = "Validação do cartão";
        $metadata['origin'] = Transaction::ORIGIN_TRANSACTION;

        $request->metadata = $metadata;

        //Test Item
        $itemPrice = new CreateOrderItemRequest();
        $itemPrice->description = "Validação do cartão";
        $itemPrice->quantity = 1;
        $itemPrice->amount = 500;
        $itemPrice->code = $plan->id;
        $itemPrice->category = Plan::ORDER_ITEM_CATEGORY_COURSE;
        $request->items = array($itemPrice);

        //Order payments
        $payments = [new CreatePaymentRequest()];
        $payments[0]->paymentMethod = Constants::MUNDIPAGG_PAYMENT_METHOD_CREDIT_CARD;
        $payments[0]->creditCard = new CreateCreditCardPaymentRequest();
        $payments[0]->amount = 500;
        $payments[0]->creditCard->installments = $plan->installment;
        $payments[0]->creditCard->statementDescriptor = 'Teste Xgrow';
        $payments[0]->creditCard->cardId = $cardId;

        //Create credit card address
        if ($orderInfo->getAddressInfo()) {
            $payments[0]->creditCard->card = new CreateCardRequest();
            $payments[0]->creditCard->card->billingAddress = $this->getBillingAddress($orderInfo->getAddressInfo());
        }
        $request->payments = $payments;

        $mundipaggService = new MundipaggService();
        return $mundipaggService->createClientOrder($request);
    }

    public function cancelCharge($chargeId): bool
    {
        $mundipaggService = new MundipaggService();
        $mundipaggService->getClient()->getCharges()->cancelCharge($chargeId);
        return true;
    }

    public function confirmCreditCard(GetOrderResponse $order)
    {
        $mundipaggService = new MundipaggService();
        if (!isset($order->charges)) {
            return $mundipaggService->getClient()->getOrders()->getOrder($order->id);
        }

        $capture = true;
        foreach ($order->charges as $cod => $charge) {
            if ($charge->lastTransaction->status == Constants::MUNDIPAGG_AUTHORIZED_PENDING_CAPTURE) {
                $capture &= true;
            } else {
                $capture &= false;
            }
        }

        if ($capture) {
            //Capture charges
            foreach ($order->charges as $cod => $charge) {
                try {
                    $contextBody = '';
                    $callback = new HttpCallBack(null, function (HttpContext $httpContext) use (&$contextBody) {
                        // Really sensitive data, must be sent to another destination OTHER THAN COMMON LOG
                        $contextBody = $httpContext->getResponse()->getRawBody();
                    });

                    $chargesApi = $mundipaggService->getClient()->getCharges();
                    $chargesApi->setHttpCallBack($callback);
                    $chargesApi->captureCharge($charge->id);
                } catch (APIException $exception) {
                    Log::debug('checkout:mundipagg:captureCharge:responseBody', [
                        'exception_info' => [
                            'message' => $exception->getMessage(),
                            'code' => $exception->getCode(),
                            'file' => $exception->getFile(),
                            'line' => $exception->getLine(),
                            //'trace' => $exception->getTraceAsString(),
                        ],
                        'raw_body' => $contextBody,
                    ]);

                    //cancel charge if exception APIException
                    if ($exception->getCode() == 409) {
                        $chargesApi->cancelCharge($charge->id);
                        throw new CaptureFailedException();
                    }

                    throw $exception;
                }
            }
            $status = Constants::MUNDIPAGG_PAID; //Set paid order
        } else {
            //Cancel authorized charges
            foreach ($order->charges as $cod => $charge) {
                if ($charge->lastTransaction->status == Constants::MUNDIPAGG_AUTHORIZED_PENDING_CAPTURE) {
                    $mundipaggService->getClient()->getCharges()->cancelCharge($charge->id);
                }
            }
            $status = Constants::MUNDIPAGG_FAILED; //Cancel order
        }

        $orderCancelStatus = new UpdateOrderStatusRequest();
        $orderCancelStatus->status = $status;
        $mundipaggService->getClient()->getOrders()->updateOrderStatus($order->id, $orderCancelStatus);

        return $mundipaggService->getClient()->getOrders()->getOrder($order->id);
    }

    public function getBillingAddress(AddressInfo $addressInfo)
    {
        $address = new CreateAddressRequest();
        $address->zipCode = preg_replace('/[^0-9]/', '', $addressInfo->getZipcode());
        //$address->number = $request->address_number;
        $address->city = $addressInfo->getCity();
        //$address->complement = $request->address_comp;
        $address->country = 'BR';
        //$address->neighborhood = $request->address_district;
        $address->state = $addressInfo->getState();
        //$address->street = $request->address_street;
        $address->line1 = "{$addressInfo->getNumber()}, {$addressInfo->getStreet()}, {$addressInfo->getDistrict()}";
        return $address;
    }

}
