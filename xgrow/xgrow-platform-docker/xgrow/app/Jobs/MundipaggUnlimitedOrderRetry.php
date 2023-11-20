<?php

namespace App\Jobs;

use App\Payment;
use App\Services\Charges\NoLimitChargeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Mail\Mailable as MailableContract;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;

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
    public $to;
    public $mail;
    public $cancelSubscriptionOnFail;
    public $dryRun;
    public $baseDate;

    public $commandCorrelationId;

    public function __construct(
        Payment $payment,
        MailableContract $mail,
        bool $cancelSubscriptionOnFail = false,
        bool $dryRun = false,
        ?string $baseDate = null
    ) {
        $this->payment = $payment;
        $this->mail = $mail;
        $this->cancelSubscriptionOnFail = $cancelSubscriptionOnFail;
        $this->dryRun = $dryRun;
        $this->baseDate = $baseDate;

        $this->commandCorrelationId = Config::get('command_correlation_id');
    }

    public function handle(NoLimitChargeService $noLimitChargeService)
    {
        Config::set('command_correlation_id', $this->commandCorrelationId);

        $noLimitChargeService->createPaymentOrder(
            $this->payment,
            $this->mail,
            $this->cancelSubscriptionOnFail,
            $this->dryRun
        );
    }

}
