<?php

namespace App\Services\Finances\Payment\Manual;

use App\CreditCard;
use App\Logs\ChargeLog;
use App\Payment;
use App\PaymentPlan;
use App\Services\Finances\Objects\Constants;
use App\Services\Finances\Objects\PriceTag;
use App\Services\Finances\Product\ProductInformationService;
use App\Services\Finances\Transaction\GatewayTransaction;
use App\Services\Mundipagg\CreditCardRecurrenceService;
use App\Services\Mundipagg\MundipaggSplitService;
use App\Services\Mundipagg\SplitService;
use App\Services\MundipaggService;
use App\Transaction;
use Carbon\Carbon;
use MundiAPILib\Models\CreateCreditCardPaymentRequest;
use MundiAPILib\Models\CreateOrderItemRequest;
use MundiAPILib\Models\CreateOrderRequest;
use MundiAPILib\Models\CreatePaymentRequest;

class ManualPaymentService
{

    private bool $isFromPlatform = false;

    private ProductInformationService $productInformationService;
    private CreditCardRecurrenceService $mundipaggRecurrenceService;

    public function __construct(
        ProductInformationService $productInformationService,
        CreditCardRecurrenceService $mundipaggRecurrenceService
    ) {
        $this->productInformationService = $productInformationService;
        $this->mundipaggRecurrenceService = $mundipaggRecurrenceService;
    }

    public function setIsFromPlatform(bool $isFromPlatform = true): self
    {
        $this->isFromPlatform = $isFromPlatform;
        return $this;
    }

    /**
     * @param  \App\Payment  $payment
     * @param $creditCard
     * @throws \App\Services\Finances\Payment\Exceptions\FailedTransaction
     * @throws \MundiAPILib\APIException
     */
    public function chargeFailedUnlimited(Payment $payment, $creditCard): void
    {
        $orderRequest = new CreateOrderRequest();
        $orderRequest->closed = true;
        $orderRequest->customerId = $payment->customer_id;

        $context = ChargeLog::getContext();

        $metadata = [
            'obs' => "Venda sem limite (parcela {$payment->installment_number} de {$payment->installments})",
            'unlimited_sale' => true,
            'total_installments' => $payment->installments,
            'origin' => $this->isFromPlatform ? Transaction::ORIGIN_PLATFORM : Transaction::ORIGIN_LEARNING_AREA,
            'hostname-dispatcher' => $context['hostname-dispatcher'] ?? '',
            'hostname-runner' => gethostname(),
        ];

        if (strlen($payment->coupon_id)) {
            $metadata['cupom_id'] = $payment->coupon_id;
        }

        $items = [];
        foreach ($payment->plans as $cod => $plan) {
            $planAmount = str_replace('.', '', $plan->getPrice());

            $itemPrice = new CreateOrderItemRequest();
            $itemPrice->description = $plan->name;
            $itemPrice->quantity = 1;
            $itemPrice->amount = $planAmount;
            $itemPrice->code = $plan->id;
            $items[] = $itemPrice;
        }
        $orderRequest->items = $items;

        $mainPriceTag = null;
        $orderBumpPriceTags = [];
        foreach ($payment->plans as $cod => $plan) {
            if (($plan->pivot->type == 'order_bump') ||
                (!empty($orderbumpId) && $orderbumpId == $plan->id)
            ) {
                $metadata['order_bump_plan_id'] = $plan->id;
                $metadata['order_bump_plan'] = $plan->name;
                $metadata['order_bump_value'] = $plan->price;

                $orderBumpPriceTags[] = PriceTag::fromDecimal($plan->id, $plan->getPrice());
            } else {
                $metadata['plan_id'] = $plan->id;
                $metadata['plan'] = $plan->name;
                $metadata['value'] = $plan->price;

                $mainPriceTag = PriceTag::fromDecimal($plan->id, $plan->getPrice());
            }
        }

        $orderRequest->metadata = $metadata;

        $paymentRequest = new CreatePaymentRequest();
        $paymentRequest->paymentMethod = 'credit_card';
        $paymentRequest->amount = str_replace('.', '', $payment->price);
        $paymentRequest->creditCard = new CreateCreditCardPaymentRequest();
        $paymentRequest->creditCard->cardId = $creditCard->card_id;

        $splitService = new SplitService($payment->platform_id);
        $splitResult = $splitService->disableAnticipation()->getPaymentSplit(
            $payment->plans_value,
            $payment->price,
            $mainPriceTag,
            $orderBumpPriceTags,
            $payment->installments,
        );

        $mundipaggSplitService = new MundipaggSplitService($payment->platform_id);

        $paymentRequest->split = $mundipaggSplitService->generateMundipaggSplit($splitResult);
        $paymentRequest->metadata = $splitResult->getMetadata();
        $orderRequest->payments = array($paymentRequest);

        ChargeLog::withContext(['request' => $orderRequest]);

        $mundipaggService = new MundipaggService();
        $orderResult = $mundipaggService->getClient()->getOrders()->createOrder($orderRequest);

        $payment->gateway = 'mundipagg';
        $payment->status = $orderResult->status;
        $payment->order_id = $orderResult->id;
        $payment->customer_id = $orderResult->customer->id;
        $payment->order_code = $orderResult->code;

        if ($orderResult->status != Constants::MUNDIPAGG_PAID) {
            ChargeLog::withContext(['result' => $orderResult]);
            ChargeLog::error('Error on unlimitedOrder');

            $this->paymentFailed($payment);

            throw GatewayTransaction::makeExceptionForOrder($orderResult); // throw FailedTransaction
        }

        ChargeLog::info('Successful charge on unlimitedOrder');

        foreach ($orderResult->charges as $cod => $charge) {
            $payment->charge_id = $charge->id;
            $payment->charge_code = $charge->code;
            $payment->payment_source = $this->isFromPlatform ? Payment::PAYMENT_SOURCE_PLATFORM : Payment::PAYMENT_SOURCE_LA;

            $splits = $charge->lastTransaction->split ?? [];

            foreach ($splits as $c => $split) {
                $value = $split->amount / 100;
                if ($split->options->chargeProcessingFee) {
                    $payment->service_value = $value; //Xgrow
                } else {
                    $payment->customer_value = $value; //Customer
                }
            }
        }

        $paymentPlans = $payment->plans();
        $paymentPlans->updateExistingPivot($paymentPlans->allRelatedIds(), [
            'status' => PaymentPlan::STATUS_PAID,
        ]);

        $payment->confirmed_at = Carbon::now();
        $payment->status = Payment::STATUS_PAID;
        $payment->save();
        $payment->plans()->syncWithoutDetaching($payment->plans);
    }

    /**
     * @param  \App\Payment  $payment
     * @param  \App\CreditCard  $creditCard
     * @throws \App\Services\Finances\Payment\Exceptions\FailedTransaction
     * @throws \MundiAPILib\APIException
     */
    public function chargeFailedRecurrence(Payment $payment, CreditCard $creditCard): void
    {
        $recurrence = $payment->recurrences->first();

        $orderRequest = new CreateOrderRequest();
        $orderRequest->customerId = $recurrence->subscriber->customer_id;
        $parcelNumber = $recurrence->current_charge + 1;
        $orderRequest->items = $this->productInformationService->getItems($recurrence->plan, $parcelNumber);

        $context = ChargeLog::getContext();

        $metadata = $this->productInformationService->getOrderMetadata($recurrence->plan);

        $metadata['obs'] = "Venda de assinatura (pagamento numero {$parcelNumber}) via LA";
        $metadata['origin'] = $this->isFromPlatform ? Transaction::ORIGIN_PLATFORM : Transaction::ORIGIN_LEARNING_AREA;
        $metadata['hostname-dispatcher'] = $context['hostname-dispatcher'] ?? '';
        $metadata['hostname-runner'] = gethostname();

        $orderRequest->metadata = $metadata;

        // single installment on manual method
        $paymentData = $this->mundipaggRecurrenceService->getPaymentRecurrence($recurrence, $creditCard->card_id);

        $orderRequest->payments = $paymentData->getPayments();

        ChargeLog::withContext(['request' => $orderRequest]);

        $mundipaggService = new MundipaggService();
        $orderResult = $mundipaggService->getClient()->getOrders()->createOrder($orderRequest);

        if ($orderResult->status !== 'paid') {
            ChargeLog::withContext(['result' => $orderResult]);
            ChargeLog::error('Error on recurrenceOrder');

            $this->paymentFailed($payment);

            throw GatewayTransaction::makeExceptionForOrder($orderResult); // throw FailedTransaction
        }

        ChargeLog::info('Successful charge on recurrenceOrder');

        $charge = $orderResult->charges[0] ?? null; // single charge

        $payment->confirmed_at = Carbon::now();
        $payment->status = Payment::STATUS_PAID;
        $payment->gateway = 'mundipagg';
        $payment->order_id = $orderResult->id;
        $payment->charge_id = $charge->id ?? null;
        $payment->customer_id = $orderResult->customer->id;
        $payment->charge_code = $charge->code ?? null;
        $payment->order_code = $orderResult->code;
        $payment->payment_source = $this->isFromPlatform ? Payment::PAYMENT_SOURCE_PLATFORM : Payment::PAYMENT_SOURCE_LA;
        $payment->save();

        $paymentPlans = $payment->plans();
        $paymentPlans->updateExistingPivot($paymentPlans->allRelatedIds(), [
            'status' => PaymentPlan::STATUS_PAID,
        ]);

        $recurrence->current_charge = $parcelNumber;
        $recurrence->last_payment = $orderResult->createdAt;
        $recurrence->card_id = $creditCard->id;
        $recurrence->save();
    }

    public function paymentFailed(Payment $payment)
    {
        $plans = $payment->plans;

        if ($payment->status != Payment::STATUS_PENDING) {
            $payment->status = Payment::STATUS_FAILED;

            $paymentPlans = $payment->plans();
            $paymentPlans->updateExistingPivot($paymentPlans->allRelatedIds(), [
                'status' => PaymentPlan::STATUS_FAILED,
            ]);
        }

        $payment->save();
        $payment->plans()->syncWithoutDetaching($plans);
    }
}
