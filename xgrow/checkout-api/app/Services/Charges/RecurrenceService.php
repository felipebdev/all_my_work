<?php

namespace App\Services\Charges;

use App\Plan;
use App\Recurrence;
use App\Services\Finances\Objects\Constants;
use App\Subscriber;
use Carbon\Carbon;

class RecurrenceService
{
    private ?string $affiliateId = null;

    public function withAffiliateId(string $affiliateId): self
    {
        $this->affiliateId = $affiliateId;
        return $this;
    }

    /**
     * Creates a recurrence for a subscription
     *
     * @param  \App\Subscriber  $subscriber
     * @param  \App\Plan  $plan
     * @param  string  $paymentMethod
     * @param  string  $orderNumber
     * @param  \Carbon\Carbon  $invoiceAt
     * @param  \Carbon\Carbon|null  $paidAt
     * @return \App\Recurrence
     */
    public function createSubscriptionRecurrence(
        Subscriber $subscriber,
        Plan $plan,
        string $paymentMethod,
        int $originalInstallments,
        string $orderNumber,
        Carbon $invoiceAt,
        ?Carbon $paidAt = null
    ): Recurrence {
        $creditCardId = $paymentMethod == Constants::XGROW_CREDIT_CARD
            ? $subscriber->credit_card_id
            : null;

        $objRecurrence = new Recurrence();
        $objRecurrence->subscriber_id = $subscriber->id;
        $objRecurrence->recurrence = $plan->recurrence;
        $objRecurrence->last_invoice = $invoiceAt;
        $objRecurrence->last_payment = $paidAt;
        $objRecurrence->card_id = $creditCardId;
        $objRecurrence->current_charge = 1;
        $objRecurrence->default_installments = $originalInstallments;
        $objRecurrence->affiliate_id = $this->affiliateId ?? null;
        $objRecurrence->type = Recurrence::TYPE_SUBSCRIPTION;
        $objRecurrence->total_charges = null;
        $objRecurrence->plan_id = $plan->id;
        $objRecurrence->order_number = $orderNumber;
        $objRecurrence->payment_method = $paymentMethod;
        $objRecurrence->save();

        return $objRecurrence;
    }
}
