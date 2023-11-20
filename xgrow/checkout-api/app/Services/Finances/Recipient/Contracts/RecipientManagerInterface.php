<?php

namespace App\Services\Finances\Recipient\Contracts;

use App\Services\Finances\Recipient\Objects\RecipientResponse;
use App\Services\Mundipagg\Objects\RecipientData;

interface RecipientManagerInterface
{

    /**
     * Create a new recipient on payment gateway.
     *
     * @param  \App\Services\Mundipagg\Objects\RecipientData  $recipientData
     * @return \App\Services\Finances\Recipient\Objects\RecipientResponse
     * @throws \App\Exceptions\RecipientFailedException
     */
    public function createRecipient(RecipientData $recipientData): RecipientResponse;

    /**
     * Get information about a recipient
     *
     * @param  string  $recipientId
     * @return \App\Services\Finances\Recipient\Objects\RecipientResponse
     * @throws \App\Exceptions\Finances\ActionFailedException
     * @throws \App\Exceptions\Finances\InvalidRecipientException
     */
    public function obtainRecipient(string $recipientId): RecipientResponse;

    /**
     * Get anticipation delay for a recipient.
     *
     * @param  string  $recipientId
     * @return int Number of days
     */
    public function getAnticipationDelay(string $recipientId): int;

}
