<?php

namespace App\Services\Finances\Transfer\Drivers;

use App\Services\Finances\Objects\Coin;
use App\Services\Finances\Transfer\Contracts\TransferInterface;
use App\Services\Finances\Transfer\Objects\TransferFilter;
use App\Services\Finances\Transfer\Objects\TransferResponse;

class VoidTransfer implements TransferInterface
{
    public function createTransfer(
        Coin $coin,
        string $recipientId,
        string $description = '',
        array $metadata = []
    ): TransferResponse {
        return TransferResponse::empty();
    }

    public function listTransfers(?TransferFilter $transferFilter = null): array
    {
        return [TransferResponse::empty()];
    }

    public function getTransfer(string $transferId): TransferResponse
    {
        return TransferResponse::empty();
    }

    public function cancelTransfer(string $transferId): TransferResponse
    {
        return TransferResponse::empty();
    }
}
