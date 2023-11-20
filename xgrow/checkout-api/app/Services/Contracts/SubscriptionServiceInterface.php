<?php

namespace App\Services\Contracts;

use App\Payment;
use App\Plan;
use App\Subscriber;

interface SubscriptionServiceInterface
{
    public function hasActiveSubscription($subscriber_id, $platform_id, $plan_id): bool;

    /**
     * Mark failed payment for subscription WITHOUT canceling it or related payments
     *
     * @param  \App\Subscriber  $subscriber
     * @param  \App\Plan  $plan
     * @return bool
     */
    public function markSubscriptionWithFailedPayment(Subscriber $subscriber, Plan $plan): bool;

    public function cancelSubscription(
        Subscriber $subscriber,
        Plan $plan,
        ?string $subscriptionCancellationReason = null
    ): bool;

    public function enableSubscriptionByPayment(Payment $payment);

    /**
     * Cancel all subscriptions and unpaid payments (pending/failed) based on orderNumber
     *
     * @param  string  $orderNumber
     * @param  string|null  $cancellationReason
     * @param  int|null  $cancellationUserId
     * @return bool
     */
    public function cancelSubscriptionsAndPayments(
        string $orderNumber,
        ?string $cancellationReason = null,
        ?int $cancellationUserId = null
    ): bool;

    /**
     * Cancel unpaid payments (pending/failed) based on orderNumber
     *
     * @param  string  $orderNumber
     * @param  string|null  $cancellationReason
     * @param  int|null  $cancellationUserId
     * @return bool
     */
    public function cancelUnpaidPayments(
        string $orderNumber,
        ?string $cancellationReason = null,
        ?int $cancellationUserId = null
    ): bool;

}
