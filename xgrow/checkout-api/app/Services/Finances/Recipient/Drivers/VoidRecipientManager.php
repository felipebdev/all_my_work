<?php

namespace App\Services\Finances\Recipient\Drivers;

use App\Services\Finances\Recipient\Contracts\RecipientManagerInterface;
use App\Services\Finances\Recipient\Objects\RecipientResponse;
use App\Services\Mundipagg\Objects\RecipientData;

class VoidRecipientManager implements RecipientManagerInterface
{

    public function createRecipient(RecipientData $recipientData): RecipientResponse
    {
        return RecipientResponse::empty();
    }

    public function obtainRecipient(string $recipientId): RecipientResponse
    {
        return RecipientResponse::empty();
    }

    public function getAnticipationDelay(string $recipientId): int
    {
        return 29;
    }
}
