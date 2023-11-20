<?php

namespace App\Services\Finances\Payment\Strategies;

use App\Facades\Whatsapp;
use App\Mail\SendMailPixCode;
use App\Payment;
use App\Services\EmailService;
use App\Services\Finances\Customer\MundipaggCustomerService;
use App\Services\Finances\Objects\OrderInfo;
use App\Services\Finances\Objects\PaymentOrderResult;
use App\Services\Finances\Payment\Contracts\PaymentMethodOrder;
use App\Services\Finances\Transaction\GatewayTransaction;
use App\Services\Mundipagg\Objects\OrderResult;
use App\Services\MundipaggService;
use App\Services\Pagarme\PagarmeCheckoutService;
use App\Subscriber;
use App\Utils\TriggerIntegrationJob;
use Illuminate\Support\Facades\Log;
use MundiAPILib\Models\CreateOrderRequest;

class PixOrder implements PaymentMethodOrder
{

    use TriggerIntegrationJob;

    private PagarmeCheckoutService $service;
    private MundipaggCustomerService $customerService;

    public function __construct(PagarmeCheckoutService $checkoutService, MundipaggCustomerService $customerService)
    {
        $this->service = $checkoutService;
        $this->customerService = $customerService;
    }

    public function order(OrderInfo $orderInfo, Subscriber $subscriber): OrderResult
    {
        $mundipaggCustomerId = $this->customerService->getCustomerIdOrCreate($orderInfo, $subscriber);

        // get mundipagg customer
        $mundipaggService = new MundipaggService();
        $mundipaggCustomer = $mundipaggService->getClient()->getCustomers()->getCustomer($mundipaggCustomerId);

        $orderRequest = new CreateOrderRequest();
        $orderRequest->customer = $mundipaggCustomer;

        $orderResult = $this->service->createOrder($orderInfo, $subscriber, $orderRequest);

        $valid = [
            'paid',
            'pending',
        ];

        $orderResponse = $orderResult->getMundipaggOrderResponse();
        if (in_array($orderResponse->status, $valid)) {
            return $orderResult;
        }

        Log::error(json_encode($orderResponse));
        GatewayTransaction::createFailedTransaction($orderInfo->getPlatformId(), $subscriber->id, $orderResponse);
        throw GatewayTransaction::makeExceptionForOrder($orderResponse);
    }

    public function confirmOrder(
        $orderResponse, // @todo Create wrapper object
        Subscriber $subscriber,
        Payment ...$payments
    ): PaymentOrderResult {
        $singlePayment = $payments[0]; // single payment for "PIX"

        //Status pending payment
        if ($subscriber->status != Subscriber::STATUS_ACTIVE) {
            $subscriber->status = Subscriber::STATUS_LEAD;
        }

        $this->triggerPixCreatedEvent($singlePayment);

        Log::debug('whatsapp:pix-created:test-will-publish', ['payment_id' => $singlePayment->id]);

        $platform = $singlePayment->platform;
        if ($platform->notifications_whatsapp ?? false) {
            Log::debug('whatsapp:pix-created:publishing', ['payment_id' => $singlePayment->id]);
            Whatsapp::pixCreated($singlePayment);
        }

        return new PaymentOrderResult(true, true);
    }

    public function sendPixMail(Subscriber $subscriber, Payment $payment)
    {
        $usersTo = [$subscriber->email];
        $mail = new SendMailPixCode($subscriber->platform_id, $subscriber, $payment);

        EmailService::mail($usersTo, $mail);
    }
}
