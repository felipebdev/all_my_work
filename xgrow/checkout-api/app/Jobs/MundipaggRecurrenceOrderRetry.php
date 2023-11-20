<?php

namespace App\Jobs;

use App\Logs\ChargeLog;
use App\Payment;
use App\Recurrence;
use App\Services\Charges\SubscriptionRetryChargeService;
use App\Services\Mundipagg\CreditCardRecurrenceService;
use App\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * This job is dispatched when retrying a recurring payment that has previously failed.
 *
 * @package App\Jobs
 */
class MundipaggRecurrenceOrderRetry implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    public $recurrence;
    public $failedPayment;
    public int $mailId;
    public bool $skipEmail = false;

    public $context;

    public function __construct(Recurrence $recurrence, Payment $failedPayment, int $mailId, bool $skipEmail = false)
    {
        $this->onQueue('xgrow-jobs:ruler');

        $this->recurrence = $recurrence;
        $this->failedPayment = $failedPayment;
        $this->mailId = $mailId;
        $this->skipEmail = $skipEmail;

        $this->context = ChargeLog::getContext();
    }

    public function handle(
        SubscriptionRetryChargeService $subscriptionRetryChargeService,
        CreditCardRecurrenceService $creditCardRecurrenceService
    ) {
        ChargeLog::withContext($this->context);
        ChargeLog::withContext(['recurrence_id' => $this->recurrence->id]);

        // check again if recurrence still needs to be retried
        $shouldRetry = $subscriptionRetryChargeService->shouldRetryChargeSubscription($this->failedPayment);
        if (!$shouldRetry) {
            // recurrence is no longer needed to be retried (eg: paid via LA, manual cancel, etc)
            ChargeLog::info('Subscription skipped on handle');
            return false;
        }

        $creditCardRecurrenceService->setTransactionOrigin(Transaction::ORIGIN_RULER);
        $creditCardRecurrenceService->skipEmail($this->skipEmail);
        $creditCardRecurrenceService->createRecurrenceOrder($this->recurrence, $this->failedPayment, $this->mailId);
    }
}
