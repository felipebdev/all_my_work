<?php

namespace App\Services\Mundipagg;

use App\CreditCard;
use App\Services\Finances\Objects\Coin;
use App\Services\Finances\Objects\Constants;
use App\Services\Finances\Objects\OrderInfo;
use App\Services\Finances\Payment\Contracts\GatewayOrder;
use App\Services\Finances\Product\ProductInformationService;
use App\Services\Mundipagg\Objects\OrderResult;
use App\Services\Mundipagg\Objects\PaymentData;
use App\Services\MundipaggService;
use App\Subscriber;
use App\Transaction;
use MundiAPILib\Models\CreateCreditCardPaymentRequest;
use MundiAPILib\Models\CreateOrderRequest;
use MundiAPILib\Models\CreatePaymentRequest;
use MundiAPILib\Models\GetOrderResponse;

class CheckoutUnlimitedSaleService implements GatewayOrder
{

    protected ProductInformationService $productInformationService;

    public function __construct()
    {
        $this->productInformationService = new ProductInformationService();
    }

    const CODE_INSUFFICIENT_FUNDS = 1016;

    /**
     * @param  OrderInfo  $orderInfo
     * @param  null  $cardId
     * @param  CreateOrderRequest  $orderRequest
     * @return GetOrderResponse
     */
    public function createOrder(OrderInfo $orderInfo, Subscriber $subscriber, CreateOrderRequest $orderRequest): OrderResult
    {
        $platform = $orderInfo->finder->rememberPlatform();
        $plan = $orderInfo->finder->rememberPlan();

        $orderInfo->validateCcInfo(); // validate credit card info

        // Validates if total value given by credit card is correct
        $this->productInformationService->validateCreditCardTotalValues($orderInfo);

        //Get Order items
        $orderRequest->items = $this->productInformationService->getItems($plan);

        //Set metadata
        $orderRequest->metadata = $this->getUnlimitedOrderMetadata($orderInfo);

        //Order payments
        $paymentData = $this->getPaymentCreditCard($orderInfo, $subscriber);
        $orderRequest->payments = $paymentData->getPayments();

        $mundipaggService = new MundipaggService();
        $orderResponse = $mundipaggService->createClientOrder($orderRequest);

        return OrderResult::fromMundipagg($orderResponse, $paymentData->getProducerSplits());
    }

    public function getUnlimitedOrderMetadata(OrderInfo $orderInfo)
    {
        $plan = $orderInfo->finder->rememberPlan();

        $creditCardData = $orderInfo->getCcInfo();

        $totalInstallments = $creditCardData[0]['installment']; // single credit card

        $metadata = $this->productInformationService->getOrderMetadata($plan, $orderInfo);

        $metadata['unlimited_sale'] = true;
        $metadata['total_installments'] = $totalInstallments;
        $metadata['obs'] = "Venda sem limite (parcela 1 de {$totalInstallments}) via Checkout";
        $metadata['origin'] = Transaction::ORIGIN_TRANSACTION;

        return $metadata;
    }


    /**
     * Get First payment for unlimited sale
     * @param  OrderInfo  $orderInfo
     * @param  Subscriber|null  $subscriber
     * @return array
     * @throws \Exception
     */
    public function getPaymentCreditCard(OrderInfo $orderInfo, Subscriber $subscriber = null): PaymentData
    {
        $plan = $orderInfo->finder->rememberPlan();

        $creditCards = $orderInfo->getCcInfo();

        $paymentRequests = [];
        $producerSplits = [];
        foreach ($creditCards as $creditCard) {
            $value = $creditCard['value'];

            $installment = $creditCard['installment'];

            $valueWithInterest = $plan->getInstallmentValue($value, $installment);

            $installmentValue = Coin::fromDecimal($value)->firstInstallment($installment)->getDecimal();

            //get payment split
            $splitService = new SplitService($orderInfo->getPlatformId());

            $affiliate = $orderInfo->finder->rememberAffiliate();
            if ($affiliate) {
                $splitService->withAffiliate($affiliate);
            }

            $producerSplit = $splitService
                ->setUnlimitedMode(SplitService::UNLIMITED_PRECALCULATED)
                ->getPaymentSplit(
                    $installmentValue,
                    $valueWithInterest,
                    $orderInfo->priceTag->planPriceTag(),
                    $orderInfo->priceTag->orderBumpPriceTags(),
                    $installment
                );

            $producerSplits[] = $producerSplit;

            $mundipaggSplitService = new MundipaggSplitService($orderInfo->getPlatformId());

            // build request options
            $paymentRequest = new CreatePaymentRequest();
            $paymentRequest->amount = (string) round($valueWithInterest * 100);
            $paymentRequest->paymentMethod = Constants::MUNDIPAGG_PAYMENT_METHOD_CREDIT_CARD;
            $paymentRequest->creditCard = new CreateCreditCardPaymentRequest();
            $paymentRequest->creditCard->installments = 1;
            $paymentRequest->creditCard->operationType = 'auth_only';
            $paymentRequest->creditCard->capture = false;
            $paymentRequest->creditCard->cardId = CreditCard::findOrFail($subscriber->credit_card_id)->card_id;
            $paymentRequest->split = $mundipaggSplitService->generateMundipaggSplit($producerSplit);
            $paymentRequest->metadata = $producerSplit->getMetadata();

            $paymentRequests[] = $paymentRequest;
        }

        return PaymentData::pack($producerSplits, $paymentRequests);
    }

}
