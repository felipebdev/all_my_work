<?php

namespace App\Services\Finances\Transfer\Contracts;

use App\Services\Finances\Objects\Coin;
use App\Services\Finances\Transfer\Objects\TransferFilter;
use App\Services\Finances\Transfer\Objects\TransferResponse;

interface TransferInterface
{
    /**
     * Create transfer
     *
     * @param  \App\Services\Finances\Objects\Coin  $coin
     * @param  string  $recipientId
     * @param  string  $description
     * @param  array  $metadata
     * @return \App\Services\Finances\Transfer\Objects\TransferResponse
     */
    public function createTransfer(
        Coin $coin,
        string $recipientId,
        string $description = '',
        array $metadata = []
    ): TransferResponse;

    /**
     * List transfers with optional filter
     *
     * @param  \App\Services\Finances\Transfer\Objects\TransferFilter|null  $transferFilter
     * @return iterable<TransferResponse>
     */
    public function listTransfers(?TransferFilter $transferFilter = null): iterable;

    /**
     * Get single transfer
     *
     * @param  string  $transferId
     * @return \App\Services\Finances\Transfer\Objects\TransferResponse
     * @throws \App\Exceptions\Finances\TransferNotFoundException
     */
    public function getTransfer(string $transferId): TransferResponse;

    /**
     * Cancel single transfer
     *
     * @param  string  $transferId
     * @return \App\Services\Finances\Transfer\Objects\TransferResponse
     * @throws \App\Exceptions\Finances\TransferNotFoundException
     */
    public function cancelTransfer(string $transferId): TransferResponse;
}
