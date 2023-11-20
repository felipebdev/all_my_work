<?php

namespace App\Services\Finances\Product;

use App\Exceptions\Checkout\CouponNotFoundException;
use App\Exceptions\Checkout\CouponUnavailableException;
use App\Http\Controllers\CouponController;
use App\Payment;
use App\Plan;
use App\Services\Finances\Objects\Constants;
use App\Services\Finances\Objects\OrderInfo;
use App\Services\Finances\Payment\BasePaymentService;
use App\Services\Finances\Payment\Exceptions\InvalidOrderException;
use App\Services\Mundipagg\MundipaggSplitService;
use App\Services\Mundipagg\Objects\PaymentData;
use App\Services\Mundipagg\SplitService;
use App\Subscriber;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use MundiAPILib\Models\CreateBoletoPaymentRequest;
use MundiAPILib\Models\CreatePaymentRequest;
use MundiAPILib\Models\CreatePixPaymentRequest;

class ProductInformationService extends BasePaymentService
{
    public function getTotalAmountForRecurrence(Plan $plan, $parcel_number = null): int
    {
        $amount = 0;
        foreach ($this->getItems($plan, $parcel_number) as $item) {
            $amount += $item->amount;
        }

        return $amount;
    }

    /**
     * Calculate the expected value of an Order
     *
     * @param  \App\Services\Finances\Objects\OrderInfo  $orderInfo
     * @return float Value in BRL
     * @throws \App\Exceptions\Checkout\CouponNotFoundException
     * @throws \App\Exceptions\Checkout\CouponUnavailableException
     */
    public function expectedValue(OrderInfo $orderInfo): float
    {
        $parcelNumber = 1; // Expected value uses a single installment as a reference
        $plan = $orderInfo->finder->rememberPlan();
        $orderBumpsBag = $orderInfo->getOrderBumpsBag();

        $itens = $this->getItems($plan, $parcelNumber, $orderBumpsBag);

        $totalValue = 0;
        foreach ($itens as $item) {
            $itemValue = round(($item->amount / 100), 2);

            //If main product check coupon
            if ($item->category == 'product' && strlen($orderInfo->getCoupom()) > 0) {
                $cupom = $this->getCupom(
                    $orderInfo->getPlatformId(),
                    $orderInfo->getPlanId(),
                    $orderInfo->getCoupom(),
                    $orderInfo->getSubscriberId()
                );

                if ($cupom) {
                    $itemValue = $itemValue - $cupom->getDiscountValue($itemValue);
                }
            }

            $totalValue = $totalValue + $itemValue;
        }

        return round($totalValue, 2);
    }

    /**
     * Get total value from credit cards and check if it's the same amount as expected
     *
     * @param  \App\Services\Finances\Objects\OrderInfo  $orderInfo
     * @throws \App\Exceptions\Checkout\CouponNotFoundException
     * @throws \App\Exceptions\Checkout\CouponUnavailableException
     * @throws \App\Services\Finances\Payment\Exceptions\InvalidOrderException
     */
    public function validateCreditCardTotalValues(OrderInfo $orderInfo)
    {
        $creditCardCollection = new Collection($orderInfo->getCcInfo());

        $totalValue = $creditCardCollection->sum(fn($creditCard) => $creditCard['value']);

        $this->checkTotalValues($orderInfo, round($totalValue, 2));
    }

    /**
     * Validate total values from multimeans ("payments" field)
     *
     * @param  \App\Services\Finances\Objects\OrderInfo  $orderInfo
     * @throws \App\Exceptions\Checkout\CouponNotFoundException
     * @throws \App\Exceptions\Checkout\CouponUnavailableException
     * @throws \App\Services\Finances\Payment\Exceptions\InvalidOrderException
     */
    public function validatePaymentsTotalValues(OrderInfo $orderInfo)
    {
        $paymentsCollection = $orderInfo->getPayments();

        $totalValue = $paymentsCollection->sum('value');

        $this->checkTotalValues($orderInfo, round($totalValue, 2));
    }

    /**
     * Verifica se o total informado nos cartões não é diferente do total do pedido
     *
     * @param  \App\Services\Finances\Objects\OrderInfo  $orderInfo
     * @param  float  $valueGiven  Value given by the user as a float ("BRL")
     * @throws \App\Exceptions\Checkout\CouponNotFoundException
     * @throws \App\Exceptions\Checkout\CouponUnavailableException
     * @throws \App\Services\Finances\Payment\Exceptions\InvalidOrderException
     */
    protected function checkTotalValues(OrderInfo $orderInfo, float $valueGiven)
    {
        $plan = $orderInfo->finder->rememberPlan();

        $expectedValue = $this->expectedValue($orderInfo);

        if ($expectedValue != $valueGiven) {
            Log::withContext(['orderInfo' => (array) $orderInfo]);
            Log::withContext(['plan' => $plan]);
            Log::withContext(['expectedValue' => $expectedValue]);
            Log::withContext(['valueGiven' => $valueGiven]);
            Log::error('checkTotalValues mismatch');

            throw new InvalidOrderException('O valor total dos cartões deve ser igual valor total da compra.');
        }
    }

    /**
     * @param  \App\Services\Finances\Objects\OrderInfo  $orderInfo
     * @return \App\Services\Mundipagg\Objects\PaymentData
     * @throws \App\Exceptions\Checkout\CouponNotFoundException
     * @throws \App\Exceptions\Checkout\CouponUnavailableException
     */
    public function getPayment(OrderInfo $orderInfo): PaymentData
    {
        $platform = $orderInfo->finder->rememberPlatform();
        $plan = $orderInfo->finder->rememberPlan();

        //Order payments
        $payment = new CreatePaymentRequest();

        if ($orderInfo->getPaymentMethod() == Constants::XGROW_BOLETO) {
            //Boleto
            $payment->paymentMethod = Payment::TYPE_PAYMENT_BILLET;
            $payment->boleto = new CreateBoletoPaymentRequest();
            $payment->boleto->instructions = "Pagar até o vencimento";
            $payment->boleto->dueAt = ProductPaymentService::boletoCheckoutDueAt($plan);
            $orderInfo->setInstallmentSelected(1); //Se boleto força numero de parcelas para 1
        } elseif ($orderInfo->getPaymentMethod() == Constants::XGROW_PIX) {
            $payment->paymentMethod = Payment::TYPE_PAYMENT_PIX;
            $payment->pix = new CreatePixPaymentRequest();
            $payment->pix->expiresIn = ProductPaymentService::pixExpirationInSeconds($plan);
        }

        //Sum items amount
        $value = $this->expectedValue($orderInfo);
        $installment = ($orderInfo->getInstallmentSelected() > 0)
            ? $orderInfo->getInstallmentSelected()
            : 1;
        $valueWithInterest = $installment * $plan->getInstallmentValue($value, $installment);
        $payment->amount = str_replace('.', '', (string) number_format($valueWithInterest, 2, '.', '.'));

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
            $orderInfo->getInstallmentSelected()
        );

        $mundipaggSplitService = new MundipaggSplitService($platform->id);

        $payment->split = $mundipaggSplitService->generateMundipaggSplit($producerSplit);
        $payment->metadata = $producerSplit->getMetadata();

        return PaymentData::pack([$producerSplit], [$payment]);
    }

    /**
     * @param  string  $platformId
     * @param  int  $planId
     * @param  string  $couponCode
     * @param  int  $subscriberId
     * @return mixed
     * @throws \App\Exceptions\Checkout\CouponNotFoundException
     * @throws \App\Exceptions\Checkout\CouponUnavailableException
     */
    private function getCupom(string $platformId, int $planId, string $couponCode, int $subscriberId)
    {
        $coupon = CouponController::findCoupon($platformId, $planId, $couponCode);

        if (!$coupon) {
            throw new CouponNotFoundException("Cupom não encontrado: {$couponCode}");
        }

        $subscriber = Subscriber::findOrFail($subscriberId);
        if (!CouponController::isAvailable($coupon, $subscriber->email)) {
            throw new CouponUnavailableException("Cupom não disponível: {$couponCode}");
        }

        return $coupon;
    }

}
