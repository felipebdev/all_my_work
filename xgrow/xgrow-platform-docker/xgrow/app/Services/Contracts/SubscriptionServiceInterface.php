<?php

namespace App\Services\Contracts;

use App\Payment;
use App\Plan;
use App\Subscriber;
use App\Subscription;

interface SubscriptionServiceInterface {
    public function cancel(Subscription $subscription, $date = null);

    /**
     * @deprecated use \App\Services\Checkout\RefundService::refund instead
     */
    public function cancelRefund(Subscription $subscription);

    /**
     * @deprecated use \App\Services\Checkout\RefundService::refund instead
     */
    public function cancelRefundPix(Subscription $subscription);

    /**
     * @deprecated use \App\Services\Checkout\RefundService::refund instead
     */
    public function cancelRefundBoleto(
        Subscription $subscription,
        string $bankCode,
        string $agency,
        ?string $agencyDigit,
        string $account,
        ?string $accountDigit,
        string $documentNumber,
        string $legalName
    );

    /**
     * @deprecated use \App\Services\Checkout\RefundService::refund instead
     */
    public function refundBoleto(
        Payment $payment,
        string $bankCode,
        string $agency,
        ?string $agencyDigit,
        string $account,
        ?string $accountDigit,
        string $documentNumber,
        string $legalName
    );

    /**
     * @deprecated use \App\Services\Checkout\RefundService::refund instead
     */
    public function refundPix(Payment $payment);

    public function hasActiveSubscription($subscriber_id, $platform_id, $plan_id): bool;

    public function markSubscriptionWithFailedPayment(Subscriber $subscriber, Plan $plan): bool;

    public function cancelSubscription(
        Subscriber $subscriber,
        Plan $plan,
        ?string $subscriptionCancellationReason = null
    ): bool;

    public function enableSubscriptionByPayment(Payment $payment);
    public function cancelSubscriptionByPayment(Payment $payment);

    /**
     * Cancel all subscriptions and pending payments based on orderNumber
     *
     * @param  string  $orderNumber
     * @param  string|null  $cancellationReason
     * @return bool
     */
    public function cancelSubscriptionsAndPayments(
        string $orderNumber,
        ?string $cancellationReason = null,
        ?int $cancellationUserId = null
    ): bool;
}
