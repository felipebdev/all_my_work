<?php

namespace App\Services\Finances\Payment\Manual;

use App\Repositories\Payments\PaymentLogRepository;
use Carbon\Carbon;

class ClientPaymentThrottlingService
{
    private PaymentLogRepository $paymentLogRepository;

    public function __construct(PaymentLogRepository $paymentLogRepository)
    {
        $this->paymentLogRepository = $paymentLogRepository;
    }

    public function canTryManualPaymentNow(int $paymentId): bool
    {
        $paymentLogs = $this->paymentLogRepository->getClientTriesByPaymentId($paymentId);

        if ($paymentLogs->count() >= 3) {
            return false; // nops
        }

        if ($paymentLogs->count() == 2) {
            $mostRecent = $paymentLogs->first();
            if ($mostRecent->created_at->addHours(24) > Carbon::now()) {
                return false;
            }
        }

        return true;
    }

    public function nextDateAllowed(int $paymentId): ?Carbon
    {
        $paymentLogs = $this->paymentLogRepository->getClientTriesByPaymentId($paymentId);

        if ($paymentLogs->count() >= 3) {
            return null; // never more
        }

        if ($paymentLogs->count() == 2) {
            $mostRecent = $paymentLogs->first();
            return $mostRecent->created_at->addHours(24);
        }

        return Carbon::now();
    }

    public function remainingTries(int $paymentId): int
    {
        $tries = $this->paymentLogRepository->countClientTriesByPaymentId($paymentId);

        $remaining = 3 - $tries;

        return max(0, $remaining);
    }
}
