<?php

namespace App\Jobs;

use App\Logs\ChargeLog;
use App\Recurrence;
use App\Services\Charges\SubscriptionChargeService;
use App\Services\Mundipagg\CreditCardRecurrenceService;
use App\Transaction;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MundipaggRecurrenceOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    public $recurrence;

    public ?Carbon $dueAt = null;

    public bool $skipEmail = false;

    public $context;

    public function __construct(Recurrence $recurrence, bool $skipEmail = false, ?Carbon $dueAt = null)
    {
        ChargeLog::debug("Dispatching Job ".__CLASS__." (ID {$recurrence->id})");

        $this->onQueue('xgrow-jobs:recurrences');

        $this->recurrence = $recurrence;
        $this->dueAt = $dueAt;
        $this->skipEmail = $skipEmail;

        $this->context = ChargeLog::getContext();

        ChargeLog::debug("Job dispatched ".__CLASS__." (ID {$recurrence->id})");
    }

    public function handle(
        SubscriptionChargeService $subscriptionChargeService,
        CreditCardRecurrenceService $mundipaggRecurrenceService
    ) {
        ChargeLog::withContext($this->context);

        $canCharge = $subscriptionChargeService->canChargeRecurrence($this->recurrence);
        if (!$canCharge) {
            ChargeLog::debug("Cant charge, Job ignored", [
                'class' => __CLASS__,
                'recurrence_id' => $this->recurrence->id,
            ]);
            return false;
        }

        ChargeLog::debug("Handling Job ".__CLASS__." (ID {$this->recurrence->id})");

        $mundipaggRecurrenceService->setTransactionOrigin(Transaction::ORIGIN_REGULAR);
        if ($this->dueAt) {
            $mundipaggRecurrenceService->setDueAt($this->dueAt);
        }
        $mundipaggRecurrenceService->skipEmail($this->skipEmail);
        $mundipaggRecurrenceService->createRecurrenceOrder($this->recurrence);

        ChargeLog::debug("Job handled ".__CLASS__." (ID {$this->recurrence->id})");
    }
}
