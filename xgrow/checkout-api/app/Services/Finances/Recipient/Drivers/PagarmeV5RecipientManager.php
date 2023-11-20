<?php

namespace App\Services\Finances\Recipient\Drivers;

use App\Exceptions\RecipientFailedException;
use App\Services\Finances\Recipient\Contracts\RecipientManagerInterface;
use App\Services\Finances\Recipient\Objects\RecipientResponse;
use App\Services\Mundipagg\Objects\RecipientData;
use App\Services\Pagarme\PagarmeRawClient;
use Exception;
use Illuminate\Support\Facades\Log;

class PagarmeV5RecipientManager implements RecipientManagerInterface
{

    private PagarmeRawClient $pagarmeRawClient;

    public function __construct()
    {
        $this->pagarmeRawClient = new PagarmeRawClient();
    }

    /**
     * @param  \App\Services\Mundipagg\Objects\RecipientData  $recipientData
     * @return \App\Services\Finances\Recipient\Objects\RecipientResponse
     * @throws \App\Exceptions\RecipientFailedException
     */
    public function createRecipient(RecipientData $recipientData): RecipientResponse
    {
        Log::withContext(['recipientData' => $recipientData]);
        Log::debug('Creating recipient on Pagarme (via API V5)');

        $this->validateDataBeforeSending($recipientData);

        $resultRecipient = $this->pagarmeRawClient->createRecipient($recipientData);

        return RecipientResponse::fromPagarmeRaw($resultRecipient);
    }

    public function obtainRecipient(string $recipientId): RecipientResponse
    {
        Log::withContext(['recipient_id' => $recipientId]);
        Log::debug('Retrieving recipient on Pagarme (via API V5)');

        $rawRecipient = $this->pagarmeRawClient->obtainRecipient($recipientId);

        return RecipientResponse::fromPagarmeRaw((object) $rawRecipient);
    }

    /**
     * @param  \App\Services\Mundipagg\Objects\RecipientData  $recipientData
     * @throws \App\Exceptions\RecipientFailedException
     */
    public function validateDataBeforeSending(RecipientData $recipientData)
    {
        $fields = [
            'name', 'email', 'description', 'document', 'bank', 'branchNumber', 'accountNumber', 'accountCheckDigit'
        ];

        $missingFields = array_filter($fields, fn($field) => strlen($recipientData->{$field}) <= 0);

        if (count($missingFields) == 0) {
            return;
        }

        $fieldsText = implode(', ', $missingFields);
        $message = "Existem dados bancários obrigatórios ausentes, verifique o cadastro: {$fieldsText}";

        throw new RecipientFailedException($message);
    }

    public function getAnticipationDelay(string $recipientId): int
    {
        throw new Exception('Not implemented, use driver Pagarme V4 preferably');
    }
}
