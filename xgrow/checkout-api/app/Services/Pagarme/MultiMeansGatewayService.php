<?php

namespace App\Services\Pagarme;

use App\Exceptions\MultimeansFailedException;
use App\Http\Controllers\LeadService;
use App\Services\Finances\Objects\Constants;
use App\Services\Finances\Objects\OrderInfo;
use App\Services\Finances\Product\ProductInformationService;
use App\Services\Finances\Product\ProductPaymentService;
use App\Services\Finances\Transaction\GatewayTransaction;
use App\Services\Mundipagg\MundipaggSplitService;
use App\Services\Mundipagg\Objects\OrderResult;
use App\Services\Mundipagg\SplitService;
use App\Services\MundipaggService;
use App\Services\Pagarme\PagarmeSdkV5\PagarmeClient;
use App\Subscriber;
use App\Transaction;
use App\Utils\TriggerIntegrationJob;
use Exception;
use Illuminate\Support\Str;
use PagarmeCoreApiLib\Models\CreateBoletoPaymentRequest;
use PagarmeCoreApiLib\Models\CreateChargeRequest;
use PagarmeCoreApiLib\Models\CreateCreditCardPaymentRequest;
use PagarmeCoreApiLib\Models\CreateOrderRequest;
use PagarmeCoreApiLib\Models\CreatePaymentRequest;

class MultiMeansGatewayService
{
    use TriggerIntegrationJob;

    protected ProductInformationService $productInformationService;

    protected MundipaggService $mundipaggService;

    private LeadService $leadService;
    private PagarmeClient $pagarmeClient;

    public function __construct(
        ProductInformationService $productInformationService,
        LeadService $leadService,
        MundipaggService $mundipaggService,
        PagarmeClient $pagarmeClient
    ) {
        $this->productInformationService = $productInformationService;
        $this->mundipaggService = $mundipaggService;
        $this->leadService = $leadService;
        $this->pagarmeClient = $pagarmeClient;
    }

    public function createOrder(
        OrderInfo $orderInfo,
        Subscriber $subscriber,
        CreateOrderRequest $orderRequest
    ): OrderResult {
        $platform = $orderInfo->finder->rememberPlatform();
        $plan = $orderInfo->finder->rememberPlan();

        $paymentsCollection = $orderInfo->getPayments();

        $creditCards = $paymentsCollection->where('payment_method', Constants::XGROW_CREDIT_CARD);
        $boleto = $paymentsCollection->where('payment_method', Constants::XGROW_BOLETO);

        $paymentMethods = $creditCards->merge($boleto); // add boleto as last
        $totalPaymentMethods = $paymentMethods->count();
        $lastIndex = $totalPaymentMethods - 1;

        $orderInfo->validatePayments(); // validate 'payments' info

        // Validates if total value given by payments is correct
        $this->productInformationService->validatePaymentsTotalValues($orderInfo);

        $this->leadService->leadOrdered($orderInfo);

        $orderResponseId = null; // OrderResponse ID (first credit card)

        $producerSplits = [];
        foreach ($paymentMethods as $index => $paymentOption) {
            $value = $paymentOption['value'];
            $installment = $paymentOption['installment'] ?? 1;

            $amount = round($value * 100);

            $valueWithInterest = ($installment) * $plan->getInstallmentValue($value, $installment);
            $amountWithInterest = round($valueWithInterest * 100);

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

            $mundipaggSplitService = new MundipaggSplitService($platform->id);

            if ($index == 0) {
                if ($paymentOption['payment_method'] != Constants::XGROW_CREDIT_CARD) {
                    throw new MultimeansFailedException('Primeiro item deve ser Cartão de Crédito');
                }

                $items = $this->productInformationService->getItems($plan, 1, $orderInfo->getOrderBumpsBag());

                $paymentRequest = new CreatePaymentRequest();
                $paymentRequest->amount = $amountWithInterest;
                $paymentRequest->split = $mundipaggSplitService->generateMundipaggSplit($producerSplit);
                $paymentRequest->metadata = $producerSplit->getMetadata();
                $paymentRequest->paymentMethod = Constants::MUNDIPAGG_PAYMENT_METHOD_CREDIT_CARD;
                $paymentRequest->creditCard = new CreateCreditCardPaymentRequest();
                $paymentRequest->creditCard->statementDescriptor = $this->formatDescriptor($platform->name);
                $paymentRequest->creditCard->installments = $installment;
                $paymentRequest->creditCard->operationType = 'auth_only';
                $paymentRequest->creditCard->capture = false;
                $paymentRequest->creditCard->cardToken = $paymentOption['token'];
                $paymentRequest->creditCard->amount = $amountWithInterest;

                $orderRequest->closed = false;
                $orderRequest->items = $items;
                $orderRequest->metadata = $this->getOrderMetadata($orderInfo);
                $orderRequest->payments = [$paymentRequest];

                $orderResponse = $this->pagarmeClient->createOrder($orderRequest);

                $lastStatus = $orderResponse->charges[0]->lastTransaction->status ?? null;
                $authorized = $lastStatus == 'authorized_pending_capture';

                $orderResponseId = $orderResponse->id;

                if (!$authorized) {
                    $this->pagarmeClient->closeOrderAsFailed($orderResponseId);
                    throw GatewayTransaction::makeExceptionForOrder($orderResponse);
                }
            } elseif ($index != $lastIndex) {
                if ($paymentOption['payment_method'] != Constants::XGROW_CREDIT_CARD) {
                    throw new MultimeansFailedException('Segundo, terceiro, etc, item deve ser Cartão de Crédito');
                }

                // not first, not last
                $charge = new CreateChargeRequest();
                $charge->orderId = $orderResponseId;
                $charge->amount = $amountWithInterest;
                $charge->payment = new CreatePaymentRequest();
                $charge->payment->split = $mundipaggSplitService->generateMundipaggSplit($producerSplit);
                $charge->payment->metadata = $producerSplit->getMetadata();
                $charge->payment->paymentMethod = Constants::MUNDIPAGG_PAYMENT_METHOD_CREDIT_CARD;
                $charge->payment->creditCard = new CreateCreditCardPaymentRequest();
                $charge->payment->creditCard->statementDescriptor = $this->formatDescriptor($platform->name);
                $charge->payment->creditCard->installments = $installment;
                $charge->payment->creditCard->operationType = 'auth_only';
                $charge->payment->creditCard->capture = false;
                $charge->payment->creditCard->cardToken = $paymentOption['token'];
                $charge->payment->creditCard->amount = $amountWithInterest;

                $chargeResponse = $this->pagarmeClient->createCharge($charge);

                $lastStatus = $chargeResponse->lastTransaction->status ?? null;
                $authorized = $lastStatus == 'authorized_pending_capture';

                if (!$authorized) {
                    $this->pagarmeClient->closeOrderAsFailed($orderResponseId);
                    throw GatewayTransaction::makeExceptionForCharge($chargeResponse);
                }
            } elseif ($index == $lastIndex) {
                if ($paymentOption['payment_method'] != Constants::XGROW_BOLETO) {
                    throw new MultimeansFailedException('Boleto deve ser último item da lista');
                }

                // last: boleto
                $charge = new CreateChargeRequest();
                $charge->orderId = $orderResponseId;
                $charge->amount = $amount; // always 1x for boleto
                $charge->payment = new CreatePaymentRequest();
                $charge->payment->split = $mundipaggSplitService->generateMundipaggSplit($producerSplit);
                $charge->payment->metadata = $producerSplit->getMetadata();
                $charge->payment->paymentMethod = Constants::MUNDIPAGG_PAYMENT_METHOD_BOLETO;
                $charge->payment->boleto = new CreateBoletoPaymentRequest();
                $charge->payment->boleto->instructions = "Pagar até o vencimento";
                $charge->payment->boleto->dueAt = ProductPaymentService::boletoCheckoutDueAt($plan);

                $chargeResponseBoleto = $this->pagarmeClient->createCharge($charge);

                $lastStatus = $chargeResponseBoleto->lastTransaction->status ?? null;
                $generated = $lastStatus == 'generated';

                if (!$generated) {
                    $this->pagarmeClient->closeOrderAsFailed($orderResponseId);
                    throw GatewayTransaction::makeExceptionForCharge($chargeResponse);
                }
            } else {
                throw new Exception('Ops, wrong index found: '.$index);
            }
        }

        $finalOrderResponse = $this->pagarmeClient->closeOrderAsPaid($orderResponseId);

        return OrderResult::fromPagarme($finalOrderResponse, $producerSplits);
    }

    private function getOrderMetadata(OrderInfo $orderInfo): array
    {
        $plan = $orderInfo->finder->rememberPlan();

        $metadata = $this->productInformationService->getOrderMetadata($plan, $orderInfo);

        $metadata['obs'] = 'Multimeans via Checkout';
        $metadata['origin'] = Transaction::ORIGIN_TRANSACTION;
        return $metadata;
    }

    private function formatDescriptor(string $platformName): string
    {
        $cleanup = removeAccentsAndEspecialChars($platformName);

        $camel = Str::camel($cleanup);

        return Str::limit($camel, 13, '');
    }

}
