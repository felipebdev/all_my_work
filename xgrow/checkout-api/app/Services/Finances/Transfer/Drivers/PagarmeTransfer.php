<?php

namespace App\Services\Finances\Transfer\Drivers;

use App\Exceptions\Finances\TransferNotFoundException;
use App\Services\Finances\Objects\Coin;
use App\Services\Finances\Transfer\Contracts\TransferInterface;
use App\Services\Finances\Transfer\Objects\TransferFilter;
use App\Services\Finances\Transfer\Objects\TransferResponse;
use App\Services\MundipaggService;
use Illuminate\Support\Facades\Log;
use PagarMe\Client;
use PagarMe\Exceptions\PagarMeException;

class PagarmeTransfer implements TransferInterface
{

    protected Client $pagarme;

    protected MundipaggService $mundipaggService;

    public function __construct()
    {
        $this->pagarme = new Client(env('PAGARME_API_KEY'));

        $this->mundipaggService = app()->make(MundipaggService::class);
    }

    public function createTransfer(
        Coin $coin,
        string $mundipagggRecipientId,
        string $description = '',
        array $metadata = []
    ): TransferResponse {
        $payload = [
            'amount' => $coin->getAmount(),
            'recipient_id' => $this->mundipaggService->convertToPagarMeRecipientId($mundipagggRecipientId),
            'metadata' => array_merge($metadata, ['description' => $description]),
        ];

        Log::withContext(['mundipagggRecipientId' => $mundipagggRecipientId]);
        Log::withContext(['payload' => $payload]);

        Log::info('PagarmeTransfer::createTransfer initiated');

        $transfer = (object) $this->pagarme->transfers()->create($payload);

        Log::info('PagarmeTransfer::createTransfer response', ['transfer' => $transfer]);

        return TransferResponse::fromPagarmeObject($transfer);
    }

    public function listTransfers(?TransferFilter $transferFilter = null): array
    {
        $filters = $this->generateFilterParams($transferFilter ?? TransferFilter::empty());

        $transfers = (array)$this->pagarme->transfers()->getList($filters);

        return array_map(fn($transfer) => TransferResponse::fromPagarmeObject($transfer), $transfers);
    }

    public function getTransfer(string $transferId): TransferResponse
    {
        try {
            $transfer = (object)$this->pagarme->transfers()->get([
                'id' => $transferId,
            ]);
            return TransferResponse::fromPagarmeObject($transfer);
        } catch (PagarMeException $e) {
            throw new TransferNotFoundException("Transfer not found (id: {$transferId})");
        }
    }

    public function cancelTransfer(string $transferId): TransferResponse
    {
        try {
            $canceledTransfer = (object)$this->pagarme->transfers()->cancel([
                'id' => $transferId,
            ]);

            return TransferResponse::fromPagarmeObject($canceledTransfer);
        } catch (PagarMeException $e) {
            throw new TransferNotFoundException("Transfer not found (id: {$transferId})");
        }
    }

    private function generateFilterParams(TransferFilter $filter): array
    {
        $payload = [];

        $payload['count'] = $filter->count ?? 1000; // Pagarme default is 10, force to 1000 if not set

        if ($filter->page) {
            $payload['page'] = $filter->page;
        }

        if ($filter->bankAccountId) {
            $payload['bank_account_id'] = $filter->bankAccountId;
        }

        if ($filter->amount) {
            $payload['amount'] = $filter->amount;
        }

        if ($filter->recipientId) {
            $payload['recipient_id'] = $this->mundipaggService->convertToPagarMeRecipientId($filter->recipientId) ?? '0';
        }

        if ($filter->createdAfter && $filter->createdBefore) {
            $payload['date_created'] = [
                '>=' . $filter->createdAfter->getTimestamp() . '000',
                '<=' . $filter->createdBefore->getTimestamp() . '999',
            ];
        } elseif ($filter->createdAfter) {
            $payload['date_created'] = '>=' . $filter->createdAfter->getTimestamp() . '000';
        } elseif ($filter->createdBefore) {
            $payload['date_created'] = '<=' . $filter->createdBefore->getTimestamp() . '999';
        }

        return $payload;
    }
}
