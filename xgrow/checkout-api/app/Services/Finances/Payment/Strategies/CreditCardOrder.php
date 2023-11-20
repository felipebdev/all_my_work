<?php

namespace App\Services\Finances\Payment\Strategies;

use App\Http\Controllers\LeadService;
use App\Payment;
use App\Services\EmailService;
use App\Services\Finances\Customer\MundipaggCustomerService;
use App\Services\Finances\Objects\OrderInfo;
use App\Services\Finances\Objects\PaymentOrderResult;
use App\Services\Finances\Payment\Contracts\PaymentMethodOrder;
use App\Services\Finances\Payment\CreditCardManagement;
use App\Services\Finances\Transaction\GatewayTransaction;
use App\Services\Mundipagg\MundipaggCheckoutService;
use App\Services\Mundipagg\Objects\OrderResult;
use App\Subscriber;
use App\Utils\TriggerIntegrationJob;
use Exception;
use Illuminate\Support\Facades\Log;
use MundiAPILib\Models\CreateOrderRequest;

class CreditCardOrder implements PaymentMethodOrder
{

    use TriggerIntegrationJob;

    private MundipaggCheckoutService $service;
    private LeadService $leadService;
    private MundipaggCustomerService $customerService;

    public function __construct(
        MundipaggCheckoutService $checkoutService,
        LeadService $leadService,
        MundipaggCustomerService $customerService
    ) {
        $this->service = $checkoutService;
        $this->leadService = $leadService;
        $this->customerService = $customerService;
    }

    public function order(OrderInfo $orderInfo, Subscriber $subscriber): OrderResult
    {
        $customerId = $this->customerService->getCustomerIdOrCreate($orderInfo, $subscriber);

        $orderRequest = new CreateOrderRequest();
        $orderRequest->customerId = $customerId;

        $plan = $orderInfo->finder->rememberPlan();

        if ($plan->freedays_type == 'free' && $plan->freedays > 0) {
            return $this->handleTrial($orderInfo, $subscriber, $orderRequest);
        } else {
            return $this->handleDefault($orderInfo, $subscriber, $orderRequest);
        }
    }

    public function confirmOrder(
        $orderResponse, // @todo Create wrapper object
        Subscriber $subscriber,
        Payment ...$payments
    ): PaymentOrderResult {
        //Confirm subscriber status
        $subscriber->status = Subscriber::STATUS_ACTIVE;

        //Log::debug('All payments from confirm order', ['payments' => $payments]); // @todo remove it after debug

        $firstPayment = $payments[0];

        $planIds = $firstPayment->plans->pluck('id')->toArray();

        $this->leadService->leadConfirmed($subscriber->id, $planIds);

        $this->triggerPaymentApprovedEvent($firstPayment, $subscriber); // trigger event only of the first payment

        $return = $this->sendConfirmationEmail($subscriber, ...$payments);

        return new PaymentOrderResult($return, false);
    }

    private function sendConfirmationEmail(Subscriber $subscriber, Payment ...$payments): bool
    {
        try {
            $emailService = new EmailService();
            $sent = $emailService->sendMailPurchaseProofAfterCheckout($subscriber->platform, $subscriber, ...$payments);

            return $sent;
        } catch (Exception $e) {
            Log::error("[SEND MAIL CHECKOUT] - ".$e->getMessage());
            return false;
        }
    }

    private function handleTrial(OrderInfo $orderInfo, $subscriber, $orderRequest): OrderResult
    {
        //Save credit cards
        /** @var CreditCardManagement $creditCardManagement */
        $creditCardManagement = resolve(CreditCardManagement::class);
        $creditCards = $creditCardManagement->saveCreditCards($orderInfo, $subscriber);

        foreach ($creditCards as $cod => $creditCard) {
            //Cria pedido de 5 reais para teste
            $result = $this->service->createTestOrder($orderInfo, $creditCard->card_id, $orderRequest);

            if ($result->status != 'paid') {

                $this->leadService->leadDenied($orderInfo);

                Log::error(json_encode($result));
                throw GatewayTransaction::makeExceptionForOrder($result);
            }

            $orderRequest->closed = false;
            //Cancela pedido de teste
            $this->service->cancelCharge($result, $orderInfo->getPlatformId());
        }

        return OrderResult::fromMundipagg($result);
    }

    private function handleDefault(OrderInfo $orderInfo, $subscriber, $orderRequest): OrderResult
    {
        $orderResult = $this->service->createOrder($orderInfo, $subscriber, $orderRequest);

        $valid = [
            'paid',
            'pending',
        ];

        $orderResponse = $orderResult->getMundipaggOrderResponse();
        if (!in_array($orderResponse->status, $valid)) {

            $this->leadService->leadDenied($orderInfo);

            Log::error(json_encode($orderResponse));
            GatewayTransaction::createFailedTransaction($orderInfo->getPlatformId(), $subscriber->id, $orderResponse);
            throw GatewayTransaction::makeExceptionForOrder($orderResponse);
        }

        return $orderResult;
    }
}
