<?php

namespace App\Services\Finances\Balance\Contracts;

use App\Services\Finances\Balance\Objects\BalanceResponse;

interface RecipientBalanceInterface
{

    /**
     * Get recipient's balance
     *
     * @param  string  $recipientId
     * @return \App\Services\Finances\Balance\Objects\BalanceResponse
     * @throws \App\Exceptions\Finances\RecipientNotExistsException
     */
    public function getBalance(string $recipientId): BalanceResponse;

}
