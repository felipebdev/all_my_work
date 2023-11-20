<?php

namespace App\Services\Finances\Payment\Strategies;

use App\Facades\Whatsapp;
use App\Mail\SendMailBankSlip;
use App\Payment;
use App\Services\EmailService;
use App\Services\Finances\Customer\MundipaggCustomerService;
use App\Services\Finances\Objects\OrderInfo;
use App\Services\Finances\Objects\PaymentOrderResult;
use App\Services\Finances\Payment\Contracts\PaymentMethodOrder;
use App\Services\Finances\Transaction\GatewayTransaction;
use App\Services\Mundipagg\MundipaggCheckoutService;
use App\Services\Mundipagg\Objects\OrderResult;
use App\Subscriber;
use App\Utils\TriggerIntegrationJob;
use Illuminate\Support\Facades\Log;
use MundiAPILib\Models\CreateOrderRequest;

class BoletoOrder implements PaymentMethodOrder
{

    use TriggerIntegrationJob;

    private MundipaggCheckoutService $service;
    private MundipaggCustomerService $customerService;

    public function __construct(MundipaggCheckoutService $checkoutService, MundipaggCustomerService $customerService)
    {
        $this->service = $checkoutService;
        $this->customerService = $customerService;
    }

    public function order(OrderInfo $orderInfo, Subscriber $subscriber): OrderResult
    {
        $customerId = $this->customerService->getCustomerIdOrCreate($orderInfo, $subscriber);

        $orderRequest = new CreateOrderRequest();
        $orderRequest->customerId = $customerId;

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
    ): PaymentOrderResult
    {
        $singlePayment = $payments[0]; // single payment for "boleto"

        //Status pending payment
        if ($subscriber->status != Subscriber::STATUS_ACTIVE) {
            $subscriber->status = Subscriber::STATUS_LEAD;
        }

        $this->triggerBankSlipCreatedEvent($singlePayment);

        Log::debug('whatsapp:boleto-created:test-will-publish', ['payment_id' => $singlePayment->id]);

        $platform = $singlePayment->platform;
        if ($platform->notifications_whatsapp ?? false) {
            Log::debug('whatsapp:boleto-created:publishing', ['payment_id' => $singlePayment->id]);
            Whatsapp::boletoCreated($singlePayment);
        }

        //Send boleto mails
        foreach (Payment::where('order_id', '=', $orderResponse->id)->get() as $cod => $payment) {
            //Send Boleto Mail
            $this->sendBoletoMail($subscriber, $payment);
        }

        return new PaymentOrderResult(true, true);
    }


    public function sendBoletoMail(Subscriber $subscriber, Payment $payment): void
    {
        $mail = new SendMailBankSlip($subscriber->platform_id, $subscriber, $payment);

        EmailService::mail([$subscriber->email], $mail);
    }

}
