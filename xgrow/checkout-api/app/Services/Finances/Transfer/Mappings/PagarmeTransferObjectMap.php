<?php

namespace App\Services\Finances\Transfer\Mappings;

use App\Services\Finances\Transfer\Objects\TransferResponse;

class PagarmeTransferObjectMap
{
    public static function status($status): ?string
    {
        return [
            'pending_transfer' => TransferResponse::STATUS_PENDING,
            'transferred' => TransferResponse::STATUS_TRANSFERRED,
            'failed' => TransferResponse::STATUS_FAILED,
            'processing' => TransferResponse::STATUS_PROCESSING,
            'canceled' => TransferResponse::STATUS_CANCELED,
        ][$status] ?? null;
    }
}
