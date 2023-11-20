<?php

namespace App\Jobs;

use App\Payment;
use App\Recurrence;
use App\Services\Mundipagg\RecurrenceOrderService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Mail\Mailable as MailableContract;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;

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
    public $mail;
    public $cancelSubscriptionOnFail;
    public $dryRun;
    public $baseDate;

    public $commandCorrelationId;

    /**
     * MundipaggRecurrenceOrderRetry constructor.
     *
     * @param  \App\Recurrence  $recurrence  Recurrence handled by job
     * @param  bool  $cancelSubscriptionOnFail  If true, cancel subscription if payment fails again
     * @param  \Illuminate\Contracts\Mail\Mailable  $mail  Email contents
     */
    public function __construct(
        Recurrence $recurrence,
        Payment $failedPayment,
        MailableContract $mail,
        bool $cancelSubscriptionOnFail = false,
        bool $dryRun = false,
        ?string $baseDate = null
    ) {
        $this->recurrence = $recurrence;
        $this->failedPayment = $failedPayment;
        $this->mail = $mail;
        $this->cancelSubscriptionOnFail = $cancelSubscriptionOnFail;
        $this->dryRun = $dryRun;
        $this->baseDate = $baseDate;

        $this->commandCorrelationId = Config::get('command_correlation_id');
    }

    public function handle()
    {
        Config::set('command_correlation_id', $this->commandCorrelationId);

        $recurrenceOrderService = new RecurrenceOrderService();
        $recurrenceOrderService->createRecurrenceOrder(
            $this->recurrence,
            $this->mail,
            $this->failedPayment,
            $this->cancelSubscriptionOnFail,
            $this->dryRun,
            $this->baseDate
        );
    }
}
