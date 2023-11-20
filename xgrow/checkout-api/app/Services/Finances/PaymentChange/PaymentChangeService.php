<?php

namespace App\Services\Finances\PaymentChange;

use App\CreditCard;
use App\Exceptions\Finances\PaymentChange\PaymentChangeInvalidException;
use App\Exceptions\Finances\PaymentChange\PaymentChangeNotAllowedException;
use App\Exceptions\NotImplementedException;
use App\Payment;
use App\Repositories\Payments\PaymentChangeRepository;
use App\Services\Finances\Objects\CreditCardInfo;
use App\Services\Finances\Payment\CreditCardManagement;
use App\Subscriber;

class PaymentChangeService
{
    public const INTERVAL_IN_SECONDS = 24 * 60 * 60; // 24h in seconds

    protected CreditCardManagement $creditCardManagement;
    protected PaymentChangeRepository $paymentChangeRepository;

    public function __construct(
        CreditCardManagement $creditCardManagement,
        PaymentChangeRepository $paymentChangeRepository
    ) {
        $this->creditCardManagement = $creditCardManagement;
        $this->paymentChangeRepository = $paymentChangeRepository;
    }

    /**
     * @param  \App\Payment  $payment
     * @param  string  $newPaymentMethod
     * @param  array  $ccInfo
     * @throws \App\Exceptions\Finances\PaymentChange\PaymentChangeInvalidException
     * @throws \App\Exceptions\Finances\PaymentChange\PaymentChangeNotAllowedException
     * @throws \App\Exceptions\NotImplementedException
     * @throws \MundiAPILib\APIException
     */
    public function changePaymentMethodOrCardInfo(Payment $payment, string $newPaymentMethod, array $ccInfo = [])
    {
        $paymentType = $payment->type;
        if ($paymentType == Payment::TYPE_SUBSCRIPTION) {
            $modifications = $this->handleSubscription($payment, $newPaymentMethod, $ccInfo);
        } elseif ($paymentType == Payment::TYPE_UNLIMITED) {
            $modifications = $this->handleUnlimited($payment, $newPaymentMethod, $ccInfo);
        } else {
            throw new NotImplementedException('Tipo de pagamento não suportado');
        }

        $this->paymentChangeRepository->saveHistory($payment, $modifications);
    }

    /**
     * @param  \App\Subscriber  $subscriber
     * @param  array  $ccInfo
     * @return \App\CreditCard
     * @throws \MundiAPILib\APIException
     * @throws \MundiAPILib\Exceptions\ErrorException
     */
    private function updateSubscriberCreditCard(Subscriber $subscriber, array $ccInfo): CreditCard
    {
        $creditCardInfo = CreditCardInfo::fromCcInfo($ccInfo);

        $creditCard = $this->creditCardManagement->saveCreditCard($subscriber, $creditCardInfo);

        $subscriber->credit_card_id = $creditCard->id;
        $subscriber->save();

        return $creditCard;
    }

    private function handleSubscription(Payment $payment, string $newPaymentMethod, array $ccInfo): array
    {
        $subscriber = $payment->subscriber;

        $recurrence = $payment->recurrences->first();

        $currentPaymentMethod = $recurrence->payment_method;

        $modifications = [];

        if ($currentPaymentMethod == Payment::TYPE_PAYMENT_CREDIT_CARD) {
            if ($currentPaymentMethod != $newPaymentMethod) {
                // only credit_card -> credit_card
                throw new PaymentChangeNotAllowedException("Credit card can't be changed to other payment method");
            }

            $this->updateSubscriberCreditCard($subscriber, $ccInfo);

            $modifications['type_payment'] = 'credit_card';

            return $modifications;
        } else {
            if ($currentPaymentMethod == $newPaymentMethod) {
                throw new PaymentChangeInvalidException('New payment method must differ');
            }

            if ($newPaymentMethod == Payment::TYPE_PAYMENT_CREDIT_CARD) {
                $this->updateSubscriberCreditCard($subscriber, $ccInfo);
            }


            $recurrence->payment_method = $newPaymentMethod;
            $recurrence->save();

            $payment->type_payment = $currentPaymentMethod; // Set current payment method on live model, do NOT save
            $modifications['type_payment'] = $newPaymentMethod;
        }

        return $modifications;
    }

    private function handleUnlimited(Payment $payment, string $newPaymentMethod, array $ccInfo): array
    {
        if ($newPaymentMethod != Payment::TYPE_PAYMENT_CREDIT_CARD) {
            throw new PaymentChangeNotAllowedException('New payment must be a credit card');
        }

        $subscriber = $payment->subscriber;
        $this->updateSubscriberCreditCard($subscriber, $ccInfo);

        $modifications = [];
        $modifications['type_payment'] = 'credit_card';

        return $modifications;
    }

    public function hasReachedLimit(array $subscriberIds): bool
    {
        $changes = $this->paymentChangeRepository->listChangesInGivenInterval($subscriberIds, self::INTERVAL_IN_SECONDS);

        if ($changes->count() >= 3) {
            return true;
        }

        return false;
    }

}
