<?php

namespace App\Services\Finances;

use App\Coupon;
use App\Payment;
use App\PaymentPlan;
use App\Plan;
use App\Services\Finances\Objects\Coin;
use App\Services\Finances\Objects\Constants;
use App\Services\Finances\Objects\OrderBumpsBag;
use App\Services\Finances\Objects\OrderInfo;
use App\Services\Finances\Product\ProductAmountService;
use App\Services\Finances\Product\ProductPaymentService;
use App\Services\Finances\Split\DetailedSplitService;
use App\Services\Mundipagg\Calculator\CalculatorFactory;
use App\Services\Mundipagg\Calculator\Objects\OrderValues;
use App\Services\Mundipagg\Objects\OrderResult;
use App\Services\Mundipagg\SplitService;
use App\Subscriber;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use MundiAPILib\Models\GetOrderResponse;

class PaymentStoreService
{
    private string $paymentSource = '';

    private ?OrderBumpsBag $orderBumpsBag = null;

    private ?Carbon $dueDate = null;

    public bool $isUpsell = false;

    public bool $isMultimeans = false;
    public bool $isManual = false;

    /**
     * Set Payment Source
     *
     * @param  string  $paymentSource  {@see Payment::PAYMENT_SOURCE_*}
     * @return PaymentStoreService
     */
    public function setPaymentSource(string $paymentSource): self
    {
        $this->paymentSource = $paymentSource;
        return $this;
    }

    /**
     * Include order bumps in the payment
     *
     * @param  \App\Services\Finances\Objects\OrderBumpsBag  $orderBumpsBag
     * @return PaymentStoreService
     */
    public function withOrderBumpsBag(OrderBumpsBag $orderBumpsBag): self
    {
        $this->orderBumpsBag = $orderBumpsBag;
        return $this;
    }

    /**
     * @param  \Carbon\Carbon|null  $dueDate
     * @return PaymentStoreService
     */
    public function setDueDate(Carbon $dueDate): self
    {
        $this->dueDate = $dueDate;
        return $this;
    }

    /**
     * @param  bool  $isUpsell
     * @return $this
     */
    public function setIsUpsell(bool $isUpsell = true): self
    {
        $this->isUpsell = $isUpsell;
        return $this;
    }

    /**
     * Set as multimeans payment type (multiple cards is NOT multimeans)
     *
     * @param  bool  $isMultimeans
     * @return $this
     */
    public function setIsMultimeans(bool $isMultimeans = true): self
    {
        $this->isMultimeans = $isMultimeans;
        return $this;
    }

    /**
     * @param  bool  $isManual
     * @return $this
     */
    public function setIsManual(bool $isManual = true): self
    {
        $this->isManual = $isManual;
        return $this;
    }

    public function storePayments(
        Subscriber $subscriber,
        OrderResult $orderResult,
        Carbon $paymentDate,
        string $orderNumber,
        $clientTaxTransaction,
        int $installmentNumber = 1
    ): Collection {
        $orderResponse = $orderResult->getOrderResponse();
        $producerSplitResults = $orderResult->getProducerSplits();

        $paymentDate = clone $paymentDate; //date only into this scope

        $charges = collect($orderResponse->charges);
        $charges = $charges->reverse(); // weird behavior of mundipagg with last payment first

        $numberOfCharges = $charges->count();

        // Get plan data
        $mainPlanId = $charges->first()->metadata['plan_id'] ?? $subscriber->plan_id;
        $mainPlan = Plan::findOrFail($mainPlanId);
        $mainPlanValue = $mainPlan->getPrice($installmentNumber);

        // Get payment type data
        $isUnlimitedSale = $orderResponse->metadata['unlimited_sale'] ?? false;

        if ($isUnlimitedSale) {
            $paymentType = Payment::TYPE_UNLIMITED;
        } elseif ($mainPlan->type_plan == Plan::PLAN_TYPE_SALE) {
            $paymentType = Payment::TYPE_SALE;
        } else {
            $paymentType = Payment::TYPE_SUBSCRIPTION;
        }

        // Get Coupon data
        $couponId = $orderResponse->metadata['cupom_id'] ?? null;
        if ($couponId) {
            $coupon = Coupon::findOrFail($couponId);
            // $coupon->timestamps = false;
            $coupon->increment('usage');
            $coupon->save();
        } else {
            $coupon = new Coupon();
        }
        $couponValue = $coupon->getDiscountValue($mainPlanValue) ?? 0;

        $percentSplit = $subscriber->platform->client->percent_split;

        $totalTaxTransaction = $numberOfCharges * $clientTaxTransaction; // one tax per charge

        $mainValues = OrderValues::fromDecimal($mainPlanValue, $couponValue, $percentSplit, $totalTaxTransaction);

        // Create a Bookkeeper
        $bookkeeper = new Bookkeeper();
        $bookkeeper->setMainValue($mainPlanId, $mainValues);

        $orderBumpsBag = $this->orderBumpsBag ?? OrderBumpsBag::empty();
        $orderBumps = $orderBumpsBag->getOrderBumpsPlans() ?? [];
        foreach ($orderBumps as $orderBump) {
            // ignore coupon and transaction tax on Order bump

            $orderBumpAmount = (new ProductAmountService())->getPromotionalPlanAmount($orderBump, $installmentNumber);

            $obValues = OrderValues::fromDecimal($orderBumpAmount / 100, 0, $percentSplit, 0);
            $bookkeeper->addValue($orderBump->id, $obValues);
        }

        $calculator = CalculatorFactory::getCalculator($paymentType);

        // Create a Bookmaker
        $bookmaker = new Bookmaker($bookkeeper, $calculator);
        if ($paymentType == Payment::TYPE_UNLIMITED) {
            $bookmaker->setNoLimitInstallment(1, $orderResponse->metadata['total_installments']);
        }

        foreach ($charges as $charge) {
            $bookmaker->addCharge($charge->id, $charge->amount);
        }

        $bookmaker->distribute();

        // Fill and Save Payment data
        $payments = new Collection;

        $index = 0;
        foreach ($charges as $charge) {
            $isCreditCard = $charge->paymentMethod == Constants::MUNDIPAGG_PAYMENT_METHOD_CREDIT_CARD;
            $isPaid = $orderResponse->status == Constants::MUNDIPAGG_PAID;

            if ($isCreditCard && $isPaid && !$this->isMultimeans) {
                $confirmedAt = Carbon::now();
            } else {
                $confirmedAt = null;
            }

            if ($this->isMultimeans) {
                $paymentStatus = Payment::STATUS_PENDING;
                $paymentPlanStatus = PaymentPlan::STATUS_PENDING;
            } elseif ($this->isManual) {
                // manual payment CAN have a future date and CAN be a credit card
                $paymentStatus = $orderResponse->status;
                $paymentPlanStatus = $orderResponse->status;
            } elseif ($paymentDate->isFuture() && $isCreditCard) {
                // Sem-limite has a future date and is credit card
                $paymentStatus = Payment::STATUS_PENDING;
                $paymentPlanStatus = PaymentPlan::STATUS_PENDING;
            } else {
                $paymentStatus = $orderResponse->status;
                $paymentPlanStatus = $orderResponse->status;
            }

            if ($this->isMultimeans) {
                $multipleMeans = true;
                $multipleMeansType = Payment::PAYMENT_MULTIMEANS_TYPE_BOLETO_CARTAO;
            } elseif ($numberOfCharges > 1) {
                // multiple cards
                $multipleMeans = true;
                $multipleMeansType = Payment::PAYMENT_MULTIMEANS_TYPE_CARTAO;
            } else {
                $multipleMeans = false;
                $multipleMeansType = null;
            }

            $payment = new Payment();
            $payment->platform_id = $subscriber->platform_id;
            $payment->subscriber_id = $subscriber->id;
            $payment->price = ($charge->paidAmount ?? $charge->amount) / 100;
            $payment->payment_date = $paymentDate;
            $payment->confirmed_at = $confirmedAt;
            $payment->status = $paymentStatus;
            $payment->order_id = $orderResponse->id;
            $payment->charge_id = $charge->id;
            $payment->customer_id = $orderResponse->customer->id;
            $payment->charge_code = $charge->code;
            $payment->order_code = $orderResponse->code;
            $payment->type_payment = $charge->paymentMethod;
            $payment->payment_source = $this->paymentSource;
            $payment->installment_number = $installmentNumber;
            $payment->installments = $orderResponse->metadata['total_installments']
                ?? $charge->lastTransaction->installments
                ?? 1;
            $payment->multiple_means = $multipleMeans;
            $payment->multiple_means_type = $multipleMeansType;

            //Set payment type (Simple sale, Unlimited sale or Subscription)
            $payment->type = $paymentType;

            if ($couponId) {
                $payment->coupon_id = $couponId;
            }

            $producerSplit = $producerSplitResults[$index];

            $payment->customer_value = $producerSplit->getCustomerAmount() / 100;
            $payment->service_value = $producerSplit->getFinalXgrowAmount() / 100;
            $payment->plans_value = ($producerSplit->getCustomerAmount() + $producerSplit->getTaxAmount()) / 100;
            $payment->tax_value = $producerSplit->getTaxAmount() / 100;
            $payment->antecipation_value = $producerSplit->getAnticipationAmount() / 100;
            $payment->order_number = $orderNumber;

            if ($charge->paymentMethod == Constants::MUNDIPAGG_PAYMENT_METHOD_BOLETO) {
                $expiresAt = $this->dueDate
                    ? ProductPaymentService::boletoRenewExpirationDate($this->dueDate)
                    : ProductPaymentService::boletoCheckoutExpirationDate($mainPlan);

                $payment->expires_at = $expiresAt;
                $payment->boleto_barcode = $charge->lastTransaction->barcode;
                $payment->boleto_qrcode = $charge->lastTransaction->qrCode;
                $payment->boleto_pdf = $charge->lastTransaction->pdf;
                $payment->boleto_url = $charge->lastTransaction->url;
                $payment->boleto_line = $charge->lastTransaction->line;
            }

            if ($charge->paymentMethod == Constants::MUNDIPAGG_PAYMENT_METHOD_PIX) {
                $payment->expires_at = $this->dueDate ?? ProductPaymentService::pixExpiresAt($mainPlan);
                $payment->pix_qrcode = $charge->lastTransaction->qrCode;
                $payment->pix_qrcode_url = $charge->lastTransaction->qrCodeUrl;
            }

            $payment->save();

            $mainPlanId = $bookkeeper->getMainId();
            $planDistribution = $bookmaker->getDistribution($mainPlanId, $charge->id);

            $plans[$mainPlanId] = [
                'tax_value' => $planDistribution->getDecimalTax(),
                'plan_value' => $planDistribution->getDecimalValueWithDiscounts(),
                'plan_price' => $planDistribution->getDecimalValueWithInterests(),
                'coupon_id' => $coupon->id,
                'coupon_code' => $coupon->code,
                'coupon_value' => $planDistribution->getDecimalCoupon(),
                'customer_value' => $planDistribution->getDecimalCustomerValue(),
                'type' => $this->isUpsell ? 'upsell' : 'product',
                'status' => $paymentPlanStatus,
            ];

            foreach ($bookkeeper->getValuesIds() as $orderBumpId) {
                $distribution = $bookmaker->getDistribution($orderBumpId, $charge->id);
                $plans[$orderBumpId] = [
                    'tax_value' => $distribution->getDecimalTax(),
                    'plan_value' => $distribution->getDecimalValueWithDiscounts(),
                    'plan_price' => $distribution->getDecimalValueWithInterests(),
                    'coupon_id' => null,
                    'coupon_code' => null,
                    'coupon_value' => 0,
                    'customer_value' => $distribution->getDecimalCustomerValue(),
                    'type' => 'order_bump',
                    'status' => $paymentPlanStatus,
                ];
            }

            $payment->plans()->sync($plans);

            $payments->push($payment);

            $index++;
        }

        $orderCode = $payment->order_code;
        Log::withContext(['order_code' => $orderCode]);

        $this->createPaymentPlanSplitForPayments($payments, $orderResult, $orderCode);

        return $payments;
    }

    /**
     * Store unlimited sale pending payments
     *
     * @param  \App\Services\Finances\Objects\OrderInfo  $orderInfo
     * @param  \App\Subscriber  $subscriber
     * @param  \MundiAPILib\Models\GetOrderResponse  $orderResponse
     * @param  \Carbon\Carbon  $paymentDate
     * @param  \App\Payment  $firstPayment
     * @param $installmentNumber
     * @param $orderResult
     * @param  float  $clientTaxTransaction
     * @return \Illuminate\Support\Collection
     * @throws \App\Exceptions\ValueMismatchException
     */
    public function storePendingPayments(
        OrderInfo $orderInfo,
        Subscriber $subscriber,
        GetOrderResponse $orderResponse,
        Carbon $paymentDate,
        Payment $firstPayment,
        $installmentNumber,
        $orderResult,
        $clientTaxTransaction = 1.5
    ): Collection {
        $charges = collect($orderResponse->charges);
        $charges = $charges->reverse(); // weird behavior of mundipagg with last payment first

        // Get plan data
        $mainPlan = $orderInfo->finder->rememberPlan();
        $mainPlanId = $mainPlan->id;
        $mainPlanValue = $mainPlan->getPrice();

        // Get payment type data
        $paymentType = Payment::TYPE_UNLIMITED;

        // Get coupon data
        $couponId = $orderResponse->metadata['cupom_id'] ?? null;
        $coupon = Coupon::findOrNew($couponId);
        $couponValue = $coupon->getDiscountValue($mainPlanValue) ?? 0;

        $percentSplit = $subscriber->platform->client->percent_split;

        $mainValues = OrderValues::fromDecimal($mainPlanValue, $couponValue, $percentSplit, $clientTaxTransaction);

        // Create a Bookkeeper
        $bookkeeper = new Bookkeeper();
        $bookkeeper->setMainValue($mainPlanId, $mainValues);

        $orderBumpsBag = $this->orderBumpsBag ?? OrderBumpsBag::empty();
        $orderBumps = $orderBumpsBag->getOrderBumpsPlans() ?? [];
        foreach ($orderBumps as $orderBump) {
            // ignore coupon and transaction tax on Order bump
            $orderBumpAmount = (new ProductAmountService())->getPromotionalPlanAmount($orderBump, $installmentNumber);

            $obValues = OrderValues::fromDecimal($orderBumpAmount / 100, 0, $percentSplit, 0);
            $bookkeeper->addValue($orderBump->id, $obValues);
        }

        $calculator = CalculatorFactory::getCalculator($paymentType);

        // Create a Bookmaker
        $bookmaker = new Bookmaker($bookkeeper, $calculator);
        $bookmaker->setNoLimitInstallment($installmentNumber, $orderResponse->metadata['total_installments']);

        foreach ($charges as $charge) {
            $bookmaker->addCharge($charge->id, $charge->paidAmount ?? $charge->amount);
        }

        $bookmaker->distribute();

        // recalculate split on 2nd, 3rd, 4th, ... installment
        $plan = $orderInfo->finder->rememberPlan();
        $firstCreditCard = $orderInfo->getCcInfo()[0]; // single credit card
        $value = $firstCreditCard['value'];
        $installment = $firstCreditCard['installment'];
        $valueWithInterest = $plan->getInstallmentValue($value, $installment);

        $installmentValue = Coin::fromDecimal($value)->otherInstallments($installment)->getDecimal();
        $splitService = new SplitService($orderInfo->getPlatformId());

        $affiliate = $orderInfo->finder->rememberAffiliate();
        if ($affiliate) {
            $splitService->withAffiliate($affiliate);
        }

        $producerSplit = $splitService
            ->disableAnticipation()
            ->setUnlimitedMode(SplitService::UNLIMITED_CALCULATION)
            ->getPaymentSplit(
                $installmentValue,
                $valueWithInterest,
                $orderInfo->priceTag->planPriceTag(),
                $orderInfo->priceTag->orderBumpPriceTags(),
                $installment,
                $installmentNumber // 2, 3, 4, ...
            );

        // Fill and Save Payment data
        $payments = new Collection;

        // Fill and Save Payment data
        $index = 0;
        foreach ($charges as $cod => $charge) {
            $payment = new Payment();
            $payment->platform_id = $subscriber->platform_id;
            $payment->subscriber_id = $subscriber->id;
            $payment->price = ($charge->paidAmount ?? $charge->amount) / 100;
            $payment->payment_date = $paymentDate;
            $payment->confirmed_at = null; // pending, not confirmed yet
            $payment->status = Payment::STATUS_PENDING;
            //$payment->order_id = $orderResponse->id;
            //$payment->charge_id = $charge->id;
            $payment->customer_id = $orderResponse->customer->id;
            $payment->type_payment = $charge->paymentMethod;
            $payment->installment_number = $installmentNumber;
            $payment->installments = $orderResponse->metadata['total_installments']
                ?? $orderInfo->getCcInfo()[$index]['installment']
                ?? 1;
            //$payment->multiple_means = $multipleMeans;
            //$payment->multiple_means_type = $multipleMeansType;

            //Set payment type (Unlimited sale)
            $payment->type = Payment::TYPE_UNLIMITED;

            if (isset($couponId)) {
                $payment->coupon_id = $couponId;
            }

            $payment->customer_value = $producerSplit->getCustomerAmount() / 100; // $calc->getDecimalCustomerValue()
            $payment->service_value = $producerSplit->getFinalXgrowAmount() / 100; // $calc->getDecimalServiceValue()
            $payment->plans_value = $installmentValue;
            $payment->tax_value = $producerSplit->getTaxAmount() / 100; // $calc->getTaxValue()
            $payment->order_number = $firstPayment->order_number ?? null;
            $payment->save();

            $mainProductId = $bookkeeper->getMainId();
            $planDistribution = $bookmaker->getDistribution($mainProductId, $charge->id);

            $plans[$mainPlanId] = [
                'tax_value' => $planDistribution->getDecimalTax(),
                'plan_value' => $planDistribution->getDecimalValueWithDiscounts(),
                'plan_price' => $planDistribution->getDecimalValueWithInterests(),
                'coupon_id' => $coupon->id,
                'coupon_code' => $coupon->code,
                'coupon_value' => $planDistribution->getDecimalCoupon(),
                'customer_value' => $planDistribution->getDecimalCustomerValue(),
                'type' => $this->isUpsell ? 'upsell' : 'product',
                'status' => PaymentPlan::STATUS_PENDING,
            ];

            foreach ($bookkeeper->getValuesIds() as $orderBumpId) {
                $distribution = $bookmaker->getDistribution($orderBumpId, $charge->id);
                $plans[$orderBumpId] = [
                    'tax_value' => $distribution->getDecimalTax(),
                    'plan_value' => $distribution->getDecimalValueWithDiscounts(),
                    'plan_price' => $distribution->getDecimalValueWithInterests(),
                    'coupon_id' => null,
                    'coupon_code' => null,
                    'coupon_value' => 0,
                    'customer_value' => $distribution->getDecimalCustomerValue(),
                    'type' => 'order_bump',
                    'status' => PaymentPlan::STATUS_PENDING,
                ];
            }

            $payment->plans()->sync($plans);
            $index++;

            $payments->push($payment);
        }

        $this->createPaymentPlanSplitForPendingPayments($payments, $orderResult);

        return $payments;
    }

    private function createPaymentPlanSplitForPayments(
        Collection $payments,
        OrderResult $orderResult,
        $orderCode
    ): array {
        $paymentIds = $payments->pluck('id')->toArray();
        $payment = $payments->first();

        if (is_null($paymentIds) || empty($paymentIds) || !is_array($paymentIds)) {
            Log::warning('No payment IDs were received', [
                'payment_ids' => $paymentIds ?? 'null',
            ]);
        }

        $details = DetailedSplitService::create(
            $payment->platform_id,
            $payment->platform->client->id,
            $paymentIds,
            $orderCode,
            $orderResult
        );

        $total = count($details) ?? 0;
        if ($total == 0) {
            Log::warning('No payment_plan_split created', [
                'payment_ids' => $paymentIds ?? 'null',
            ]);
        }

        return $details;
    }

    private function createPaymentPlanSplitForPendingPayments(Collection $payments, $orderResult): array
    {
        $paymentIds = $payments->pluck('id')->toArray();
        $payment = $payments->first();

        if (is_null($paymentIds) || empty($paymentIds) || !is_array($paymentIds)) {
            Log::warning('No payment IDs were received', [
                'payment_ids' => $paymentIds ?? 'null',
            ]);
        }

        $details = DetailedSplitService::create(
            $payment->platform_id,
            $payment->platform->client->id,
            $paymentIds,
            null,
            $orderResult
        );

        $total = count($details) ?? 0;
        if ($total == 0) {
            Log::warning('No payment_plan_split created', [
                'payment_ids' => $paymentIds ?? 'null',
            ]);
        }

        return $details;
    }


}
