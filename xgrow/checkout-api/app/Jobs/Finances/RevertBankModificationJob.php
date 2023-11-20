<?php

namespace App\Jobs\Finances;

use App\Services\Finances\BankAccount\BankAccountService;
use App\Services\Finances\BankAccount\Objects\BankModification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RevertBankModificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public BankModification $bankModification;

    public function __construct(BankModification $bankModification)
    {
        $this->bankModification = $bankModification;

        $this->queue = 'xgrow-jobs:finances:bank_account:revert';
    }

    public function handle(BankAccountService $bankAccountService)
    {
        return $bankAccountService->revertSingleBankAccountModification($this->bankModification);
    }
}
