<?php

namespace App\Jobs;

use App\Logs\ChargeLog;
use App\Payment;
use App\Services\Charges\NoLimitChargeService;
use App\Services\Charges\NoLimitRegularChargeService;
use App\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MundipaggUnlimitedOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    public $payment;

    public bool $skipEmail = false;

    public bool $updateDate = false;

    public $context;

    public function __construct(Payment $payment, bool $skipEmail = false, bool $updateDate = false)
    {
        $this->onQueue('xgrow-jobs:recurrences');

        $this->payment = $payment;
        $this->skipEmail = $skipEmail;
        $this->updateDate = $updateDate;
        $this->context = ChargeLog::getContext();
    }

    public function handle(
        NoLimitRegularChargeService $noLimitRegularChargeService,
        NoLimitChargeService $noLimitChargeService
    ) {
        ChargeLog::withContext($this->context);

        // check again if payment still needs to be retried
        $shouldRetry = $noLimitRegularChargeService->canChargePayment($this->payment);
        if (!$shouldRetry) {
            // payment is no longer needed to be retried (eg: paid via LA, manual cancel, etc)
            ChargeLog::info('No-Limit skipped on handle');
            return false;
        }

        $noLimitChargeService->setTransactionOrigin(Transaction::ORIGIN_REGULAR);
        $noLimitChargeService->skipEmail($this->skipEmail);
        $noLimitChargeService->enableUpdateDate($this->updateDate);
        $noLimitChargeService->createPaymentOrder($this->payment);
    }

}
