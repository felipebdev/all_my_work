<?php

namespace App\Services\Finances\Payment\Strategies;

use App\Facades\Whatsapp;
use App\Http\Controllers\LeadService;
use App\Mail\SendMailBankSlip;
use App\Payment;
use App\Services\EmailService;
use App\Services\Finances\Customer\PagarmeCustomerService;
use App\Services\Finances\Objects\OrderInfo;
use App\Services\Finances\Objects\PaymentOrderResult;
use App\Services\Finances\Payment\Contracts\PaymentMethodOrder;
use App\Services\Finances\Transaction\GatewayTransaction;
use App\Services\Mundipagg\Objects\OrderResult;
use App\Services\Pagarme\MultiMeansGatewayService;
use App\Subscriber;
use App\Utils\TriggerIntegrationJob;
use Illuminate\Support\Facades\Log;
use PagarmeCoreApiLib\Models\CreateOrderRequest;

class MultiMeansOrder implements PaymentMethodOrder
{

    use TriggerIntegrationJob;

    private MultiMeansGatewayService $orderService;
    private LeadService $leadService;
    private PagarmeCustomerService $customerService;

    public function __construct(
        MultiMeansGatewayService $gatewayOrder,
        LeadService $leadService,
        PagarmeCustomerService $customerService
    ) {
        $this->orderService = $gatewayOrder;
        $this->leadService = $leadService;
        $this->customerService = $customerService;
    }

    /**
     * @param  \App\Services\Finances\Objects\OrderInfo  $orderInfo
     * @param  \App\Subscriber  $subscriber
     * @return \App\Services\Mundipagg\Objects\OrderResult
     * @throws \App\Exceptions\MultimeansFailedException
     * @throws \App\Services\Finances\Payment\Exceptions\FailedTransaction
     */
    public function order(OrderInfo $orderInfo, Subscriber $subscriber): OrderResult
    {
        $customerId = $this->customerService->getCustomerIdOrCreate($orderInfo, $subscriber);

        $orderRequest = new CreateOrderRequest();
        $orderRequest->customerId = $customerId;

        $orderResult = $this->orderService->createOrder($orderInfo, $subscriber, $orderRequest);

        $valid = [
            'paid',
            'pending',
        ];

        $orderResponse = $orderResult->getPagarmeOrderResponse();
        if (!in_array($orderResponse->status, $valid)) {
            $this->leadService->leadDenied($orderInfo);

            Log::error(json_encode($orderResponse));
            GatewayTransaction::createFailedTransaction($orderInfo->getPlatformId(), $subscriber->id, $orderResponse);
            throw GatewayTransaction::makeExceptionForOrder($orderResponse);
        }

        // @fix return type

        return $orderResult;
    }

    /**
     * @param  \MundiAPILib\Models\GetOrderResponse|\PagarmeCoreApiLib\Models\GetOrderResponse|null  $orderResponse
     * @param  \App\Subscriber  $subscriber
     * @param  \App\Payment  ...$payments
     * @return \App\Services\Finances\Objects\PaymentOrderResult
     */
    public function confirmOrder(
        $orderResponse,
        Subscriber $subscriber,
        Payment ...$payments
    ): PaymentOrderResult {
        //Status pending payment
        if ($subscriber->status != Subscriber::STATUS_ACTIVE) {
            $subscriber->status = Subscriber::STATUS_LEAD;
        }

        foreach ($payments as $payment) {
            $this->triggerBankSlipCreatedEvent($payment);

            $platform = $payment->platform;
            if ($platform->notifications_whatsapp ?? false) {
                Log::debug('whatsapp:boleto-created:publishing', ['payment_id' => $payment->id]);
                Whatsapp::boletoCreated($payment);
            }
        }

        //Send boleto mails
        $boletoPayments = Payment::where('type_payment', 'boleto')
            ->where('order_id', '=', $orderResponse->id)
            ->get();

        foreach ($boletoPayments as $payment) {
            $this->sendBoletoMail($subscriber, $payment);
        }

        return new PaymentOrderResult(true, true);
    }

    public function sendBoletoMail(Subscriber $subscriber, Payment $payment): void
    {
        // @todo check email message
        $mail = new SendMailBankSlip($subscriber->platform_id, $subscriber, $payment);

        EmailService::mail([$subscriber->email], $mail);
    }

}
