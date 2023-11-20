<?php

namespace App\Services\Finances\BankAccount\Drivers;


use App\Services\Finances\BankAccount\Contracts\BankAccountInterface;
use App\Services\Finances\BankAccount\Objects\BankAccount;
use App\Services\Finances\BankAccount\Objects\BankAccountResponse;

class VoidBankAccount implements BankAccountInterface
{

    public function getBankAccount(string $recipientId): BankAccountResponse
    {
        return BankAccountResponse::empty();
    }

    public function updateBank(string $recipientId, BankAccount $bankAccount): BankAccountResponse
    {
        return BankAccountResponse::empty();
    }
}
