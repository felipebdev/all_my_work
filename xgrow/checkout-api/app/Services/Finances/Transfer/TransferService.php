<?php

namespace App\Services\Finances\Transfer;

use App\Exceptions\Finances\InsuficientFundsException;
use App\Exceptions\Finances\InvalidRecipientException;
use App\Exceptions\Finances\TransferCanceledException;
use App\Exceptions\NotImplementedException;
use App\Repositories\Finances\RecipientRepository;
use App\Services\Finances\Objects\Coin;
use App\Services\Finances\Transfer\Contracts\TransferInterface;
use App\Services\Finances\Transfer\Objects\TransferFilter;
use App\Services\Finances\Transfer\Objects\TransferResponse;
use App\TransferHistoryRepository;
use Exception;
use Illuminate\Support\Str;
use PagarMe\Exceptions\PagarMeException;

class TransferService
{

    private TransferInterface $transfers;

    private TransferHistoryRepository $history;

    private RecipientRepository $recipientRepository;

    public const MAX_FAILS = 1;

    public const INTERVAL_IN_SECONDS = 24 * 60 * 60; // 24h in seconds

    public function __construct(
        PaymentGatewayAdapter $gatewayAdapter,
        TransferHistoryRepository $history,
        RecipientRepository $recipientRepository
    ) {
        $driver = $gatewayAdapter->driver();
        if (!$driver instanceof TransferInterface) {
            throw new NotImplementedException('Transfer not implemented by driver: '.$gatewayAdapter->getDefaultDriver());
        }

        $this->transfers = $driver;
        $this->history = $history;
        $this->recipientRepository = $recipientRepository;
    }

    /**
     * @param  string  $platformId
     * @param  string  $userId
     * @param  string  $actingAs
     * @param  \App\Services\Finances\Transfer\Objects\TransferFilter  $transferFilter
     * @return iterable
     * @throws \App\Exceptions\Finances\RecipientNotFound
     */
    public function listUserTransfers(
        string $platformId,
        string $userId,
        string $actingAs,
        TransferFilter $transferFilter
    ): iterable
    {
        $recipientInfo = $this->recipientRepository->getRecipientInfoByActor($platformId, $userId, $actingAs);

        $transferFilter->recipientId = $recipientInfo->id; // ensure recipient

        return $this->transfers->listTransfers($transferFilter);
    }

    /**
     * @param  string  $platformId
     * @param  string  $userId
     * @param  string  $actingAs
     * @param  \App\Services\Finances\Objects\Coin  $coin
     * @param  string|null  $message
     * @param  array|null  $metadata
     * @return \App\Services\Finances\Transfer\Objects\TransferResponse
     * @throws \App\Exceptions\Finances\InsuficientFundsException
     * @throws \App\Exceptions\Finances\RecipientNotFound
     * @throws \PagarMe\Exceptions\PagarMeException
     */
    public function userTransfer(
        string $platformId,
        string $userId,
        string $actingAs,
        Coin $coin,
        ?string $message = '',
        ?array $metadata = []
    ): TransferResponse {
        $recipientId = $this->recipientRepository->getRecipientInfoByActor($platformId, $userId, $actingAs)->id;

        $history = $this->history->startProcess($platformId, $userId, $recipientId, $coin->getAmount());

        try {
            $transfer = $this->transfers->createTransfer($coin, $recipientId, $message ?? '', $metadata ?? []);

            $this->history->successful($history->id);

            return $transfer;
        } catch (PagarMeException $e) {
            if (Str::contains($e->getMessage(), 'Saldo insuficiente')) {
                $this->history->refused($history->id);

                throw new InsuficientFundsException('Saldo insuficiente');
            }

            $this->history->failed($history->id);

            if (app()->bound('sentry')) {
                // Some unknown error occurred, capture and report it to Sentry for further investigation
                app('sentry')->captureException($e);
            }

            throw $e;
        } catch (Exception $e) {
            // capture, mark as failed and re-throw exception
            $this->history->failed($history->id);

            if (app()->bound('sentry')) {
                // Some unknown error occurred, capture and report it to Sentry for further investigation
                app('sentry')->captureException($e);
            }

            throw $e;
        }
    }

    /**
     * Get and validates if transfer belongs to recipient
     *
     * @param  string  $platformId
     * @param  string  $userId
     * @param  string  $transferId
     * @return \App\Services\Finances\Transfer\Objects\TransferResponse
     * @throws \App\Exceptions\Finances\TransferNotFoundException
     * @throws \App\Exceptions\Finances\InvalidRecipientException
     */
    public function getSingleUserTransfer(string $platformId, string $userId, string $transferId): TransferResponse
    {
        $recipientId = $this->recipientRepository->getRecipientIdWithType($platformId, $userId)->id;

        $transfer = $this->transfers->getTransfer($transferId);

        if ($recipientId != $transfer->getSourceId()) {
            throw new InvalidRecipientException('Transfer not found for this recipient');
        }

        return $transfer;
    }

    /**
     * @param  string  $recipientId
     * @param  string  $userId
     * @param  string  $transferId
     * @return \App\Services\Finances\Transfer\Objects\TransferResponse
     * @throws \App\Exceptions\Finances\TransferNotFoundException
     * @throws \App\Exceptions\Finances\InvalidRecipientException
     */
    public function cancelUserTransfer(string $platformId, string $userId, string $transferId): TransferResponse
    {
        try {
            $transfer = $this->getSingleUserTransfer($platformId, $userId, $transferId);
        } catch (InvalidRecipientException $e) {
            throw new InvalidRecipientException('Transfer not belongs to this recipient');
        }

        $rawData = $transfer->getRawData() ?? null;
        $status = $rawData['status'] ?? null;
        if ($status == 'canceled') {
            throw new TransferCanceledException('Transfer already canceled');
        }

        return $this->transfers->cancelTransfer($transferId);
    }

    public function hasReachedLimit(string $userId): bool
    {
        $failed = $this->history->listFailed($userId, self::INTERVAL_IN_SECONDS);

        if ($failed->count() >= self::MAX_FAILS) {
            return true;
        }

        return false;
    }

}
