<?php

namespace App\Services\Finances\BankAccount\Objects;

/**
 * This class holds state of each modified Bank Account
 */
class BankModification
{
    public string $recipient_id;

    public BankAccountResponse $original;

    public BankAccountResponse $modified;
}
