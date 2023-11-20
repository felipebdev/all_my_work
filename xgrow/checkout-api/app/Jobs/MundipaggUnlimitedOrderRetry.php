<?php

namespace App\Jobs;

use App\Logs\ChargeLog;
use App\Payment;
use App\Services\Charges\NoLimitChargeService;
use App\Services\Charges\NoLimitRetryChargeService;
use App\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Used when retrying charge for a failed payment
 *
 * @package App\Jobs
 */
class MundipaggUnlimitedOrderRetry implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    public $payment;
    public int $mailId;
    public bool $skipEmail = false;

    public $context;

    public function __construct(Payment $payment, int $mailId, bool $skipEmail = false)
    {
        $this->onQueue('xgrow-jobs:ruler');

        $this->payment = $payment;
        $this->mailId = $mailId;
        $this->skipEmail = $skipEmail;

        $this->context = ChargeLog::getContext();
    }

    public function handle(
        NoLimitRetryChargeService $noLimitRetryChargeService,
        NoLimitChargeService $noLimitChargeService
    ) {
        ChargeLog::withContext($this->context);
        ChargeLog::withContext(['payment_id' => $this->payment->id ?? null]);

        // check again if payment still needs to be retried
        $shouldRetry = $noLimitRetryChargeService->shouldRetryChargePayment($this->payment);
        if (!$shouldRetry) {
            // payment is no longer needed to be retried (eg: paid via LA, manual cancel, etc)
            ChargeLog::info('No-Limit skipped on handle');
            return false;
        }

        $noLimitChargeService->setTransactionOrigin(Transaction::ORIGIN_RULER);
        $noLimitChargeService->skipEmail($this->skipEmail);
        $noLimitChargeService->createPaymentOrder($this->payment, $this->mailId);
    }

}
