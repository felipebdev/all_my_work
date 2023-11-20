<?php

namespace App\Services\Finances\Refund\Drivers;

use App\Exceptions\Finances\RefundFailedException;
use App\Exceptions\Finances\TransactionNotFoundException;
use App\Payment;
use App\PaymentPlan;
use App\Services\Finances\Objects\Constants;
use App\Services\Finances\Refund\Contracts\RefundInterface;
use App\Services\Finances\Refund\Objects\BankRefund;
use App\Services\Finances\Refund\Objects\PaymentRefund;
use App\Services\Finances\Refund\Objects\RefundResponse;
use App\Services\MundipaggService;
use Illuminate\Support\Facades\Log;
use MundiAPILib\Models\GetChargeResponse;
use MundiAPILib\Models\GetOrderResponse;
use PagarMe\Client;
use PagarMe\Exceptions\PagarMeException;

class PagarmeRefund implements RefundInterface
{

    private static string $defaultRuntimeErrorMessage = 'An error has occurred';

    protected Client $pagarme;

    public function __construct()
    {
        $this->pagarme = new Client(env('PAGARME_API_KEY'));
    }

    /**
     * @param  \App\Services\Finances\Refund\Objects\PaymentRefund  $paymentDataRefund
     * @param  \App\Services\Finances\Refund\Objects\BankRefund|null  $bankData
     * @return \App\Services\Finances\Refund\Objects\RefundResponse
     * @throws \App\Exceptions\Finances\RefundFailedException
     * @throws \App\Exceptions\Finances\TransactionNotFoundException
     */
    public function refund(PaymentRefund $paymentDataRefund, BankRefund $bankData = null): RefundResponse
    {
        Log::withContext(['paymentDataRefund' => $paymentDataRefund]);

        $payment = Payment::findOrFail($paymentDataRefund->paymentId);
        $amount = round($payment->price * 100);

        $metadata = array_merge($paymentDataRefund->metadata, ['reason' => $paymentDataRefund->reason]);

        if ($paymentDataRefund->paymentMethod == Constants::XGROW_BOLETO) {
            return $this->refundBoleto($payment->order_id, $amount, $metadata, $bankData);
        } elseif ($paymentDataRefund->paymentMethod == Constants::XGROW_CREDIT_CARD) {
            return $this->refundCreditCard($payment->charge_id, $amount, $metadata);
        } elseif ($paymentDataRefund->paymentMethod == Constants::XGROW_PIX) {
            return $this->refundPix($payment->charge_id, $amount, $metadata);
        }
    }

    public function refundPartial(PaymentRefund $paymentDataRefund, BankRefund $bankData = null): RefundResponse
    {
        $paymentPlan = PaymentPlan::find($paymentDataRefund->paymentPlanId);
        $payment = $paymentPlan->payment;
        $amount = round($paymentPlan->plan_price * 100);

        $metadata = array_merge($paymentDataRefund->metadata, ['reason' => $paymentDataRefund->reason]);

        if ($paymentDataRefund->paymentMethod == Constants::XGROW_BOLETO) {
            $refundBoleto = $this->refundBoleto($payment->order_id, $amount, $metadata, $bankData);
            $refundBoleto->setIsPartial();
            return $refundBoleto;
        } elseif ($paymentDataRefund->paymentMethod == Constants::XGROW_CREDIT_CARD) {
            $refundCreditCard = $this->refundCreditCard($payment->charge_id, $amount, $metadata);
            $refundCreditCard->setIsPartial();
            return $refundCreditCard;
        } elseif ($paymentDataRefund->paymentMethod == Constants::XGROW_PIX) {
            $refundPix = $this->refundPix($payment->charge_id, $amount, $metadata);
            $refundPix->setIsPartial();
            return $refundPix;
        }
    }

    /**
     * @param  string  $chargeId
     * @param  int  $amount
     * @param  array  $metadata
     * @return \App\Services\Finances\Refund\Objects\RefundResponse
     * @throws \App\Exceptions\Finances\RefundFailedException
     * @throws \App\Exceptions\Finances\TransactionNotFoundException
     */
    public function refundCreditCard(string $chargeId, int $amount, array $metadata = []): RefundResponse
    {
        try {
            $refunded = (object) $this->pagarme->transactions()->refund([
                'id' => $this->getPagarmeIdFromMundipagg($chargeId),
                'amount' => $amount,
                'metadata' => $metadata,
            ]);

            return RefundResponse::fromPagarmeObject($refunded);
        } catch (PagarMeException $exception) {
            $this->handleError($exception, $chargeId);
        }
    }

    private function refundPix(string $chargeId, int $amount, array $metadata = []): RefundResponse
    {
        try {
            $refunded = (object) $this->pagarme->transactions()->refund([
                'id' => $chargeId,
                'amount' => $amount,
                'metadata' => $metadata,
            ]);

            return RefundResponse::fromPagarmeObject($refunded);
        } catch (PagarMeException $exception) {
            $this->handleError($exception, $chargeId);
        }
    }

    /**
     * @param  string  $orderId
     * @param  int  $amount
     * @param  array  $metadata
     * @param  \App\Services\Finances\Refund\Objects\BankRefund|null  $bankData
     * @return \App\Services\Finances\Refund\Objects\RefundResponse
     * @throws \App\Exceptions\Finances\RefundFailedException
     * @throws \App\Exceptions\Finances\TransactionNotFoundException
     */
    public function refundBoleto(string $orderId, int $amount, array $metadata = [], BankRefund $bankData = null): RefundResponse
    {
        try {
            $orderResponse = $this->getOrderFromMundipagg($orderId);

            $id = $this->getPagarmeId($orderResponse);

            $agencyDigit = strlen($bankData->agencyDigit) > 0 ? $bankData->agencyDigit : null; // empty to null

            $refunded = (object) $this->pagarme->transactions()->refund([
                'id' => $id,
                'amount' => (string) $amount, // required on API v2
                'bank_code' => $bankData->bankCode,
                'agencia' => $bankData->agency,
                'agencia_dv' => $agencyDigit,
                'conta' => $bankData->account,
                'conta_dv' => $bankData->accountDigit,
                'document_number' => $bankData->documentNumber,
                'legal_name' => $bankData->legalName,
                'metadata' => $metadata,
            ]);

            return RefundResponse::fromPagarmeObject($refunded);
        } catch (PagarMeException $exception) {
            $this->handleError($exception, $orderId);
        }
    }

    /**
     * @param  \PagarMe\Exceptions\PagarMeException  $e
     * @param  string  $id  Order ID or Charge ID
     * @throws \App\Exceptions\Finances\TransactionNotFoundException
     * @throws \App\Exceptions\Finances\RefundFailedException
     */
    private function handleError(PagarMeException $e, string $id): void
    {
        Log::debug('Refund exception', ['exception' => $e, 'id' => $id]);

        $typeError = $e->getType();

        if ($typeError == 'not_found') {
            throw new TransactionNotFoundException("Transaction not found (id: {$id})");
        }

        $errorMessage = $this->getPrivateErrorMessage($e) ?? self::$defaultRuntimeErrorMessage;

        throw new RefundFailedException($errorMessage);
    }

    private function getPagarmeIdFromMundipagg(string $chargeId): ?string
    {
        $charge = $this->getChargeFromMundipagg($chargeId);

        $gatewayId = $charge->gatewayId ?? null;

        return $gatewayId;
    }

    private function getChargeFromMundipagg(string $chargeId): GetChargeResponse
    {
        $mundipaggService = new MundipaggService();
        $charge = $mundipaggService->getClient()->getCharges()->getCharge($chargeId);

        Log::withContext(['charge' => $charge]);

        return $charge;
    }

    private function getOrderFromMundipagg(string $orderId): GetOrderResponse
    {
        $mundipaggService = new MundipaggService();
        $order = $mundipaggService->getClient()->getOrders()->getOrder($orderId);

        Log::withContext(['order' => $order]);

        return $order;
    }

    private function getPagarmeId(GetOrderResponse $orderResponse)
    {
        $gatewayId = $orderResponse->charges[0]->gatewayId ?? null;

        return $gatewayId;
    }

    private function getAmount(GetOrderResponse $orderResponse): int
    {
        $amount = $orderResponse->charges[0]->amount ?? null;

        return $amount;
    }

    /**
     * Unsafe, ugly and slow access to private attribute of exception,
     * but this message error is extremely useful to be ignored.
     *
     * @param  \PagarMe\Exceptions\PagarMeException  $e
     */
    private function getPrivateErrorMessage(PagarMeException $e): ?string
    {
        $reflectionProperty = new \ReflectionProperty(PagarMeException::class, 'errorMessage');
        $reflectionProperty->setAccessible(true);

        return $reflectionProperty->getValue($e) ?? null;
    }

    public function checkTransactionStatus(string $mundipaggChargeId)
    {
        $pagarmeTransactionId = $this->getPagarmeIdFromMundipagg($mundipaggChargeId);

        return $this->pagarme->transactions()->get([
            'id' => $pagarmeTransactionId,
        ]);
    }

    public function getRefundStatus(string $mundipaggChargeId)
    {
        $pagarmeTransactionId = $this->getPagarmeIdFromMundipagg($mundipaggChargeId);

        $result = $this->pagarme->refunds()->getList([
            'transaction_id' => $pagarmeTransactionId,
        ]);

        return $result;
    }

}
