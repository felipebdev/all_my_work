<?php

namespace App\Services\Mundipagg;

use App\CreditCard;
use App\Http\Controllers\LeadService;
use App\Plan;
use App\Services\Finances\Objects\Constants;
use App\Services\Finances\Objects\OrderInfo;
use App\Services\Finances\Payment\Contracts\GatewayOrder;
use App\Services\Finances\Payment\PaymentCreditCardService;
use App\Services\Finances\Product\ProductInformationService;
use App\Services\Finances\Recipient\RecipientsPlanService;
use App\Services\Finances\Subscriber\SubscriberCreditCard;
use App\Services\Mundipagg\Objects\OrderResult;
use App\Services\Mundipagg\Objects\PaymentData;
use App\Services\MundipaggService;
use App\Subscriber;
use App\Transaction;
use Illuminate\Support\Str;
use MundiAPILib\Models\CreateCreditCardPaymentRequest;
use MundiAPILib\Models\CreateOrderRequest;
use MundiAPILib\Models\CreatePaymentRequest;
use MundiAPILib\Models\GetOrderResponse;

class MundipaggCheckoutService implements GatewayOrder
{

    protected ProductInformationService $productInformationService;
    protected PaymentCreditCardService $paymentCreditCardService;

    private LeadService $leadService;

    public function __construct(LeadService $leadService)
    {
        $this->productInformationService = new ProductInformationService();
        $this->paymentCreditCardService = new PaymentCreditCardService();

        $this->leadService = $leadService;
    }

    /**
     * @deprecated use \App\Finances\Payment\PaymentService::createTestOrder
     */
    public function createTestOrder(OrderInfo $orderInfo, $cardId, CreateOrderRequest $request): GetOrderResponse
    {
        return $this->paymentCreditCardService
            ->setPlatformId($orderInfo->getPlatformId())
            ->createTestOrder($orderInfo, $cardId, $request);
    }

    public function createOrder(OrderInfo $orderInfo, Subscriber $subscriber, CreateOrderRequest $orderRequest): OrderResult
    {
        $platform = $orderInfo->finder->rememberPlatform();
        $plan = $orderInfo->finder->rememberPlan();

        if ($orderInfo->getPaymentMethod() == Constants::XGROW_CREDIT_CARD) {
            $orderInfo->validateCcInfo(); // validate credit card info

            // Validates if total value given by credit card is correct
            $this->productInformationService->validateCreditCardTotalValues($orderInfo);
        }

        $this->leadService->leadOrdered($orderInfo);

        //Get Order items
        $orderRequest->items = $this->productInformationService->getItems($plan, 1, $orderInfo->getOrderBumpsBag());

        //Set metadata
        $orderRequest->metadata = $this->getOrderMetadata($orderInfo);

        //Order payments
        if ($orderInfo->getPaymentMethod() == Constants::XGROW_CREDIT_CARD) {
            $orderRequest->closed = false;
            $paymentData = $this->getPaymentCreditCard($orderInfo, $subscriber);
        } else {
            $orderRequest->closed = true;
            $paymentData = $this->productInformationService->getPayment($orderInfo);
        }

        $orderRequest->payments = $paymentData->getPayments();

        $mundipaggService = new MundipaggService();
        $orderResponse = $mundipaggService->createClientOrder($orderRequest);

        $producerSplits = $paymentData->getProducerSplits();

        //Credit card
        if ($orderInfo->getPaymentMethod() == Constants::XGROW_CREDIT_CARD) {
            //Store credit card
            foreach ($orderResponse->charges ?? [] as $cod => $charge) {
                if ($charge->paymentMethod == Constants::MUNDIPAGG_PAYMENT_METHOD_CREDIT_CARD) {
                    $card = $charge->lastTransaction->card ?? null;
                    if ($card) {
                        SubscriberCreditCard::save($subscriber, $card);
                    }
                }
            }

            //Unlimited sale
            if ($plan->unlimited_sale == true && $plan->type_plan == Plan::PLAN_TYPE_SALE && count($orderRequest->payments) == 1) {
                //if insufficient founds error code and unlimited sale is enabled
                foreach ($orderResponse->charges ?? [] as $cod => $charge) {
                    if ($charge->lastTransaction->acquirerReturnCode == CheckoutUnlimitedSaleService::CODE_INSUFFICIENT_FUNDS) {
                        $checkoutUnlimitedSale = new CheckoutUnlimitedSaleService();
                        $orderResult = $checkoutUnlimitedSale->createOrder($orderInfo, $subscriber, $orderRequest);
                        $orderResponse = $orderResult->getMundipaggOrderResponse();
                        $producerSplits = $orderResult->getProducerSplits();
                    }
                }
            }
        }

        //Capture and confirm credit card
        if ($orderResponse->status == Constants::MUNDIPAGG_PENDING && $orderInfo->getPaymentMethod() == Constants::XGROW_CREDIT_CARD) {
            $orderResponse = $this->paymentCreditCardService->setPlatformId($platform->id)->confirmCreditCard($orderResponse);
        }

        return OrderResult::fromMundipagg($orderResponse, $producerSplits);
    }

    public function getPaymentCreditCard(OrderInfo $orderInfo, Subscriber $subscriber): PaymentData
    {
        $platform = $orderInfo->finder->rememberPlatform();
        $plan = $orderInfo->finder->rememberPlan();

        $creditCards = $orderInfo->getCcInfo();

        $payments = [];
        $producerSplits = [];
        foreach ($creditCards as $creditCard) {
            $value = $creditCard['value'];

            $installment = $creditCard['installment'] ?? 1;

            $valueWithInterest = ($installment) * $plan->getInstallmentValue($value, $installment);

            //get payment split
            $splitService = new SplitService($platform->id);

            $affiliate = $orderInfo->finder->rememberAffiliate();
            if ($affiliate) {
                $splitService->withAffiliate($affiliate);
            }

            $producerSplit = $splitService->getPaymentSplit(
                $value,
                $valueWithInterest,
                $orderInfo->priceTag->planPriceTag(),
                $orderInfo->priceTag->orderBumpPriceTags(),
                $installment
            );

            $producerSplits[] = $producerSplit;

            // build request options
            $paymentRequest = new CreatePaymentRequest();
            $paymentRequest->amount = (string) round($valueWithInterest * 100);
            $paymentRequest->paymentMethod = Constants::MUNDIPAGG_PAYMENT_METHOD_CREDIT_CARD;
            $paymentRequest->creditCard = new CreateCreditCardPaymentRequest();
            $paymentRequest->creditCard->statementDescriptor = $this->formatDescriptor($platform->name);
            $paymentRequest->creditCard->installments = $installment;
            $paymentRequest->creditCard->operationType = 'auth_only';
            $paymentRequest->creditCard->capture = false;

            //For upsell and multiple cards send last four digits and brand card
            if (isset($creditCard['last_four_digits']) && isset($creditCard['brand'])) {
                $findCard = CreditCard::where('subscriber_id', '=', $subscriber->id)
                    ->where('brand', '=', $creditCard['brand'])
                    ->where('last_four_digits', '=', $creditCard['last_four_digits'])
                    ->first();
                if ($findCard) {
                    $paymentRequest->creditCard->cardId = $findCard->card_id;
                }
            } elseif (isset($creditCard['card_id'])) {
                // card_id from OneClickBuy
                $paymentRequest->creditCard->cardId = $creditCard['card_id'];
            } else {
                $paymentRequest->creditCard->cardToken = $creditCard['token'];
            }

            $mundipaggSplitService = new MundipaggSplitService($platform->id);

            $paymentRequest->split = $mundipaggSplitService->generateMundipaggSplit($producerSplit);
            $paymentRequest->metadata = $producerSplit->getMetadata();

            $payments[] = $paymentRequest;
        }

        return PaymentData::pack($producerSplits, $payments);
    }

    public function cancelCharge(GetOrderResponse $result, $platformId)
    {
        $this->paymentCreditCardService
            ->setPlatformId($platformId)
            ->cancelCharge($result->charges[0]->id);
    }

    private function formatDescriptor(string $platformName): string
    {
        $cleanup = removeAccentsAndEspecialChars($platformName);
        $camel = Str::camel($cleanup);
        return Str::limit($camel, 13, '');
    }

    private function getOrderMetadata(OrderInfo $orderInfo): array
    {
        $plan = $orderInfo->finder->rememberPlan();

        $metadata = $this->productInformationService->getOrderMetadata($plan, $orderInfo);
        $metadata['obs'] = "Venda simples/assinatura via Checkout";
        $metadata['origin'] = Transaction::ORIGIN_TRANSACTION;
        return $metadata;
    }

}
