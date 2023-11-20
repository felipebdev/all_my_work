<?php

namespace App\Services\Charges;

use App\Exceptions\NotImplementedException;
use App\Jobs\MundipaggRecurrenceOrder;
use App\Logs\ChargeLog;
use App\Recurrence;
use App\Services\Contracts\SubscriptionServiceInterface;
use App\Subscriber;
use App\Utils\TriggerIntegrationJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SubscriptionChargeService
{
    /**
     * @deprecated
     */
    private const DAYS_BEFORE = 5;

    use TriggerIntegrationJob;

    private $subscriptionService;

    private bool $dryRun = false;

    private bool $skipEmail = false;

    private Carbon $baseDate;

    public function __construct(SubscriptionServiceInterface $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
        $this->baseDate = Carbon::now();
    }

    /**
     * Allows use of a base date different from Today, useful to simulate or charge retroactively
     *
     * @param  \Carbon\Carbon  $baseDate
     * @return $this
     */
    public function setBaseDate(Carbon $baseDate): SubscriptionChargeService
    {
        $this->baseDate = $baseDate;
        return $this;
    }

    public function enableDryRun(bool $dryRun = true): SubscriptionChargeService
    {
        $this->dryRun = $dryRun;
        return $this;
    }

    public function skipEmail(bool $skipEmail = true): self
    {
        $this->skipEmail = $skipEmail;
        return $this;
    }

    /**
     * Dispatch recurrence
     *
     * @param  \App\Recurrence  $recurrence  true if dispatched, false if not applicable
     * @return bool
     */
    public function dispatchSingleRecurrence(Recurrence $recurrence): bool
    {
        ChargeLog::includeRecurrenceContext($recurrence);
        ChargeLog::withContext(['base_date' => $this->baseDate]);
        ChargeLog::withContext(['skip_mail' => $this->skipEmail]);

        Log::withContext(['email' => $recurrence->subscriber->email ?? '']);

        if (!$this->canChargeRecurrence($recurrence)) {
            Log::info('Cant charge recurrence');
            return false;
        }

        return $this->handleCreditCard($recurrence);

        //$recurrencePaymentMethod = $recurrence->payment_method;
        //if ($recurrencePaymentMethod == Recurrence::PAYMENT_METHOD_CREDIT_CARD) {
        //    return $this->handleCreditCard($recurrence);
        //} elseif ($recurrencePaymentMethod == Recurrence::PAYMENT_METHOD_BOLETO) {
        //    return $this->handleBoleto($recurrence);
        //} elseif ($recurrencePaymentMethod == Recurrence::PAYMENT_METHOD_PIX) {
        //    return $this->handlePix($recurrence);
        //} else {
        //    throw new NotImplementedException("Recurrence not implemented to this payment method: {$recurrencePaymentMethod}");
        //}
        //
        //return false;
    }

    protected function handleCreditCard(Recurrence $recurrence): bool
    {
        if (!$this->mustChargeByCreditCard($recurrence)) {
            Log::info('Ignoring credit card charge');
            return false;
        }

        ChargeLog::info("Dispatching recurrence for credit card charge", [
            'recurrence_last_invoice' => $recurrence->last_invoice
        ]);

        if (!$this->dryRun) {
            //dispatch order
            Log::info('Dispatching charge for subscriber');
            MundipaggRecurrenceOrder::dispatch($recurrence, $this->skipEmail);
        }

        return true;
    }

    /**
     * Check conditions to charge subscription
     *
     * @param  \App\Recurrence  $recurrence
     * @return bool
     */
    private function mustChargeByCreditCard(Recurrence $recurrence): bool
    {
        $neverPaid = empty($recurrence->last_payment);

        if ($neverPaid) {
            return true;
        }

        $recurrenceDays = (int) $recurrence->recurrence;

        $lastPayment = Carbon::createFromFormat('Y-m-d H:s:i', $recurrence->last_payment);
        $nextCharge = $lastPayment->addDays($recurrenceDays);

        if ($nextCharge->isSameDay($this->baseDate)) {
            return true;
        }

        ChargeLog::info("Subscription credit card ignored: sending not required", [
            'next_charge' => $nextCharge ?? null
        ]);

        return false;
    }

    /**
     * @deprecated
     */
    protected function handleBoleto(Recurrence $recurrence): bool
    {
        $lastInvoiceSent = Carbon::createFromFormat('Y-m-d H:s:i', $recurrence->last_invoice);

        $recurrenceDays = (int) $recurrence->recurrence;

        $nextDueAt = $lastInvoiceSent->clone()->addDays($recurrenceDays); // "theoretical" due date

        if ($recurrenceDays == 1) {
            $daysBefore = 0; // debug case, send on due date
        } else {
            $daysBefore = self::DAYS_BEFORE; // common case
        }

        // send invoice 5 continuous days before due date ("5 dias corridos antes do vencimento")
        $nextInvoice = $nextDueAt->clone()->subDays($daysBefore);

        if (!$nextInvoice->isSameDay($this->baseDate)) {
            ChargeLog::info("Subscription Boleto ignored: sending not required", [
                'next_invoice' => $nextInvoice ?? null
            ]);

            return false;
        }

        $dueAt = $nextDueAt;
        if ($dueAt->isWeekend()) {
            // adjust on weekends for next business day
            $dueAt->addWeekdays(1);
        }

        ChargeLog::info("Dispatching recurrence for Boleto charge");

        if (!$this->dryRun) {
            MundipaggRecurrenceOrder::dispatch($recurrence, $this->skipEmail, $dueAt);
        }

        return true;
    }

    /**
     * @deprecated
     */
    protected function handlePix(Recurrence $recurrence): bool
    {
        $lastInvoiceSent = Carbon::createFromFormat('Y-m-d H:s:i', $recurrence->last_invoice);

        $recurrenceDays = (int) $recurrence->recurrence;

        $nextDueAt = $lastInvoiceSent->clone()->addDays($recurrenceDays); // "theoretical" due date

        if ($recurrenceDays == 1) {
            $daysBefore = 0; // debug case, send on due date
        } else {
            $daysBefore = self::DAYS_BEFORE; // common case
        }

        // send invoice 5 continuous days before due date ("5 dias corridos antes do vencimento")
        $nextInvoice = $nextDueAt->clone()->subDays($daysBefore);

        if (!$nextInvoice->isSameDay($this->baseDate)) {
            ChargeLog::info("Subscription PIX ignored: sending not required", [
                'next_invoice' => $nextInvoice ?? null
            ]);

            return false;
        }

        $dueAt = $nextDueAt; // no adjustment required, PIX works on weekends

        ChargeLog::info("Dispatching recurrence for PIX charge");

        if (!$this->dryRun) {
            MundipaggRecurrenceOrder::dispatch($recurrence, $this->skipEmail, $dueAt);
        }

        return true;
    }

    /**
     * Check if recurrence satisfies conditions for charging on "due date"
     *
     * @param  \App\Recurrence  $recurrence
     * @return bool true if satisfies conditions, false otherwise
     */
    public function canChargeRecurrence(Recurrence $recurrence): bool
    {
        $subscriber = $recurrence->subscriber;
        if ($subscriber->status != Subscriber::STATUS_ACTIVE) {
            ChargeLog::info("Subscription charge unavailable: subscriber is not active");
            return false;
        }

        if ($recurrence->type != Recurrence::TYPE_SUBSCRIPTION) {
            ChargeLog::info("Subscription charge unavailable: recurrence is not a subscription");
            return false;
        }

        $plan = $recurrence->plan;

        $activeCharge = $plan->charge_until == 0 || $recurrence->current_charge < $plan->charge_until;

        if (!$activeCharge) {
            ChargeLog::info("Subscription charge unavailable: no active charge");
            return false;
        }

        $activeSubscription = $this->subscriptionService->hasActiveSubscription(
            $subscriber->id,
            $subscriber->platform_id,
            $plan->id
        );

        if (!$activeSubscription) {
            ChargeLog::info("Subscription charge unavailable: no active subscription");
            return false;
        }

        $nextPaymentDate = (new Carbon($recurrence->last_payment))->addDays($recurrence->recurrence);

        $now = Carbon::now();

        if (!$nextPaymentDate->isSameDay($now)) {
            ChargeLog::info("Subscription charge unavailable: wrong day", [
                'next_payment_date' => $nextPaymentDate->toDateTimeString(),
            ]);
            return false;
        }

        return true;
    }

}
