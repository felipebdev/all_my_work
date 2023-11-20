<?php

namespace App\Services\Finances\Recipient\Drivers;

use App\Services\Finances\Recipient\Contracts\RecipientManagerInterface;
use App\Services\Finances\Recipient\Objects\RecipientResponse;
use App\Services\Mundipagg\Objects\RecipientData;
use App\Services\MundipaggService;
use Exception;
use PagarMe\Client;

class PagarmeV4RecipientManager implements RecipientManagerInterface
{

    private Client $pagarmeClient;
    private MundipaggService $mundipaggService;

    public function __construct()
    {
        $this->pagarmeClient = new Client(env('PAGARME_API_KEY'));
        $this->mundipaggService = resolve(MundipaggService::class);
    }

    public function createRecipient(RecipientData $recipientData): RecipientResponse
    {
        throw new Exception('Not implemented, use driver Pagarme V5 preferably');
    }

    public function obtainRecipient(string $mundipaggRecipientId): RecipientResponse
    {
        $recipient = $this->pagarmeClient->recipients()->get([
            'id' => $this->mundipaggService->convertToPagarMeRecipientId($mundipaggRecipientId),
        ]);

        return RecipientResponse::fromPagarmeRaw((object) $recipient);
    }

    public function getAnticipationDelay(string $mundipaggRecipientId): int
    {
        $recipient = $this->obtainRecipient($mundipaggRecipientId);
        return $recipient->getRawData()['automatic_anticipation_1025_delay'] ?? 0;
    }
}
