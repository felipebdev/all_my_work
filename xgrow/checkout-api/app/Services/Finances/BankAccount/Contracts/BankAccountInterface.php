<?php

namespace App\Services\Finances\BankAccount\Contracts;

use App\Services\Finances\BankAccount\Objects\BankAccount;
use App\Services\Finances\BankAccount\Objects\BankAccountResponse;

interface BankAccountInterface
{

    /**
     * @param  string  $recipientId
     * @return \App\Services\Finances\BankAccount\Objects\BankAccountResponse
     * @throws \App\Exceptions\Finances\RecipientNotExistsException
     */
    public function getBankAccount(string $recipientId): BankAccountResponse;

    public function createBankAccount(BankAccount $bankAccount): BankAccountResponse;

    /**
     * @param  string  $recipientId
     * @param  \App\Services\Finances\BankAccount\Objects\BankAccount  $bankAccount
     * @return \App\Services\Finances\BankAccount\Objects\BankAccountResponse
     * @throws \App\Exceptions\Finances\InvalidBankAccountException
     * @throws \App\Exceptions\Finances\RateLimitExceededException
     * @throws \App\Exceptions\Finances\RecipientNotExistsException
     */
    public function updateBank(string $recipientId, BankAccount $bankAccount): BankAccountResponse;

}
