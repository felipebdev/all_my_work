<?php

namespace App\Services\Pagarme;

use App\Http\Controllers\LeadService;
use App\Plan;
use App\Services\Finances\Objects\Constants;
use App\Services\Finances\Objects\OrderInfo;
use App\Services\Finances\Payment\Contracts\GatewayOrder;
use App\Services\Finances\Product\ProductInformationService;
use App\Services\Finances\Product\ProductPaymentService;
use App\Services\Mundipagg\MundipaggSplitService;
use App\Services\Mundipagg\Objects\OrderResult;
use App\Services\Mundipagg\SplitService;
use App\Services\MundipaggService;
use App\Subscriber;
use App\Transaction;
use App\Utils\TriggerIntegrationJob;
use Exception;
use Illuminate\Support\Facades\Auth;
use MundiAPILib\Models\CreateOrderRequest;
use MundiAPILib\Models\GetChargeResponse;
use MundiAPILib\Models\GetOrderResponse;
use PagarMe\Client;
use stdClass;

class PagarmeCheckoutService implements GatewayOrder
{
    use TriggerIntegrationJob;

    protected ProductInformationService $productInformationService;

    protected MundipaggService $mundipaggService;

    private LeadService $leadService;

    public function __construct(LeadService $leadService)
    {
        $this->productInformationService = new ProductInformationService();
        $this->mundipaggService = new MundipaggService();

        $this->leadService = $leadService;
    }

    public function createOrder(
        OrderInfo $orderInfo,
        Subscriber $subscriber,
        CreateOrderRequest $orderRequest
    ): OrderResult {
        $this->leadService->leadOrdered($orderInfo);

        $plan = $orderInfo->finder->rememberPlan();

        $items = [];
        foreach ($this->productInformationService->getItems($plan, 1, $orderInfo->getOrderBumpsBag()) as $cod => $item) {
            $items[$cod]['id'] = (string) $item->code;
            $items[$cod]['title'] = $item->description;
            $items[$cod]['unit_price'] = $item->amount;
            $items[$cod]['quantity'] = (string) $item->quantity;
            $items[$cod]['tangible'] = true;
        }

        $installments = $orderInfo->getInstallmentSelected() > 0 ? $orderInfo->getInstallmentSelected() : 1;

        //Sum items amount
        $value = $this->productInformationService->expectedValue($orderInfo);
        $valueWithInterest = $installments * $plan->getInstallmentValue($value, $orderInfo->getInstallmentSelected());

        $splitService = new SplitService($orderInfo->getPlatformId());

        $affiliate = $orderInfo->finder->rememberAffiliate();
        if ($affiliate) {
            $splitService->withAffiliate($affiliate);
        }

        $splitResult = $splitService->getPaymentSplit(
            $value,
            $valueWithInterest,
            $orderInfo->priceTag->planPriceTag(),
            $orderInfo->priceTag->orderBumpPriceTags(),
            $installments
        );

        $orderMetadata = $this->productInformationService->getOrderMetadata($plan, $orderInfo);
        $paymentMetadata = $splitResult->getMetadata();
        $metadata = array_merge($orderMetadata, $paymentMetadata);
        $metadata['obs'] = "Venda simples/assinatura (Pagar.me) via Checkout";
        $metadata['origin'] = Transaction::ORIGIN_TRANSACTION;

        try {
            $pagarme = new Client(env('PAGARME_API_KEY'));
            $mundipaggSplitService = new MundipaggSplitService($orderInfo->getPlatformId());

            $clientRule = [
                'recipient_id' => $this->mundipaggService->convertToPagarMeRecipientId(
                    $mundipaggSplitService->getClientRecipientIdOrCreate()
                ),
                'amount' => $splitResult->getFinalClientAmount(),
                'liable' => true,
                'charge_processing_fee' => false,
                'charge_remainder' => true
            ];

            $xgrowRule = [
                'recipient_id' => $this->mundipaggService->convertToPagarMeRecipientId(
                    $mundipaggSplitService->getXgrowRecipientIdOrCreate()
                ),
                'amount' => $splitResult->getFinalXgrowAmount(),
                'liable' => false,
                'charge_processing_fee' => true,
                'charge_remainder' => false
            ];

            $splitRules = [];
            $affiliateFinalAmount = $splitResult->getAffiliateFinalAmount();
            if ($affiliateFinalAmount) {
                $affiliateRule = [
                    'recipient_id' => $this->mundipaggService->convertToPagarMeRecipientId(
                        $mundipaggSplitService->getAffiliateRecipientId($affiliate->id)
                    ),
                    'amount' => $affiliateFinalAmount,
                    'liable' => false,
                    'charge_processing_fee' => false,
                    'charge_remainder' => false
                ];

                $splitRules[] = $affiliateRule;
            }

            foreach ($splitResult->aggregateAmountByProducer() as $producerId => $producerAmount) {
                $rule = [
                    'recipient_id' => $this->mundipaggService->convertToPagarMeRecipientId(
                        $mundipaggSplitService->getProducerRecipientIdOrCreate($producerId)
                    ),
                    'amount' => $producerAmount,
                    'liable' => false,
                    'charge_processing_fee' => false,
                    'charge_remainder' => false
                ];
                $splitRules[] = $rule;
            }

            $splitRules[] = $clientRule;
            $splitRules[] = $xgrowRule;

            //Create transaction
            $transaction = [
                'amount' => (string) round($valueWithInterest * 100),
                'payment_method' => $orderInfo->getPaymentMethod(),
                'pix_expiration_date' => ProductPaymentService::pixExpiresAt($plan),
                /*'pix_additional_fields' => [
                    [
                        'name' => 'Produto',
                        'value'=>  '2'
                    ]
                ],*/
                'customer' => [
                    'external_id' => (string) $subscriber->id,
                    'name' => $orderRequest->customer->name,
                    'email' => $orderRequest->customer->email,
                    'type' => $orderRequest->customer->type == Constants::MUNDIPAGG_COMPANY
                        ? Constants::PAGARME_CORPORATION
                        : Constants::PAGARME_INDIVIDUAL,
                    'country' => strtolower($orderRequest->customer->address->country ?? 'br'),
                    'documents' => [
                        [
                            'type' => $this->getCustomerDocumentType($subscriber),
                            'number' => $this->getCustomerDocumentNumber($subscriber),
                        ]
                    ],

                    'phone_numbers' => [
                        '+'.$subscriber->phone_country_code.$subscriber->phone_area_code.$subscriber->phone_number
                    ]
                ],
                'items' => $items,
                'metadata' => $metadata,
                'split_rules' => $splitRules,
                'postback_url' => route('pagarme.transaction.status')
            ];

            $order = $pagarme->transactions()->create($transaction);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 400);
        }

        $orderResponse = $this->pagarmeToMundipaggOrderResponse($order, $orderRequest, $plan);

        return OrderResult::fromMundipagg($orderResponse, [$splitResult]);
    }

    private function getCustomerDocumentType(Subscriber $subscriber): string
    {
        $documentType = $subscriber->document_type ?? null;

        if ($documentType == Subscriber::DOCUMENT_TYPE_CPF) {
            return Constants::PAGARME_CPF;
        }

        return Constants::PAGARME_CNPJ;
    }

    private function getCustomerDocumentNumber(Subscriber $subscriber): string
    {
        $brazilianDocumentTypes = [
            Subscriber::DOCUMENT_TYPE_CPF,
            Subscriber::DOCUMENT_TYPE_CNPJ,
        ];

        $documentType = $subscriber->document_type ?? null;

        if (in_array($documentType, $brazilianDocumentTypes)) {
            return $subscriber->document_number;
        }

        return Constants::MUNDIPAGG_CNPJ_FOREIGNER;
    }

    private function pagarmeToMundipaggOrderResponse(
        $pagarmeOrder,
        CreateOrderRequest $orderRequest,
        Plan $plan
    ): GetOrderResponse {
        $orderResponse = new GetOrderResponse();
        $orderResponse->id = $pagarmeOrder->id;
        $orderResponse->currency = 'BRL';
        $orderResponse->status = $pagarmeOrder->status == Constants::PAGARME_TRANSACTION_WAITING_PAYMENT
            ? Constants::MUNDIPAGG_PENDING
            : $pagarmeOrder->status;
        $orderResponse->code = $pagarmeOrder->id;
        $orderResponse->customer = $orderRequest->customer;

        $charge = new GetChargeResponse();
        $charge->id = $pagarmeOrder->id;
        $charge->code = $pagarmeOrder->id;
        $charge->gatewayId = $pagarmeOrder->acquirer_id;
        $charge->amount = $pagarmeOrder->amount;
        $charge->paymentMethod = $pagarmeOrder->payment_method;
        $charge->status = $pagarmeOrder->status == Constants::PAGARME_TRANSACTION_WAITING_PAYMENT
            ? Constants::MUNDIPAGG_PENDING
            : $pagarmeOrder->status;
        $charge->createdAt = $pagarmeOrder->date_created;
        $charge->customer = $orderRequest->customer;
        $charge->lastTransaction = new stdClass();
        $charge->lastTransaction->qrCode = $pagarmeOrder->pix_qr_code;
        $charge->lastTransaction->qrCodeUrl = null;
        $orderResponse->charges = [$charge];

        $orderResponse->items = $this->productInformationService->getItems($plan);
        $orderResponse->closed = true;
        $orderResponse->createdAt = $pagarmeOrder->date_created;
        $orderResponse->metadata = json_decode(json_encode($pagarmeOrder->metadata), true);
        return $orderResponse;
    }

}
