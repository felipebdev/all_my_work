<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class TransferHistoryRepository
{
    /**
     * Start a new transfer process
     */
    public function startProcess(string $platformId, string $userId, string $recipientId, int $amount): TransferHistory
    {
        $model = TransferHistory::create([
            'platform_id' => $platformId,
            'user_id' => $userId,
            'recipient_id' => $recipientId,
            'amount' => $amount,
            'status' => TransferHistory::STATUS_PROCESSING,
        ]);

        return $model;
    }

    /**
     * Mark transfer history as successful
     *
     * A successful transfer is when the recipient has received the money
     */
    public function successful(string $transferHistoryId): bool
    {
        $affected = TransferHistory::where('id', $transferHistoryId)->update([
            'status' => TransferHistory::STATUS_SUCCESSFUL
        ]);

        return $affected == 1;
    }

    /**
     * Mark transfer history as refused
     *
     * A refused transfer happens when the request was understood but the recipient has not received the money.
     * This status DO NOT count on "fail block".
     *
     * @param  string  $transferHistoryId
     * @return bool
     */
    public function refused(string $transferHistoryId): bool
    {
        $affected = TransferHistory::where('id', $transferHistoryId)->update([
            'status' => TransferHistory::STATUS_REFUSED
        ]);

        return $affected == 1;
    }

    /**
     * Mark transfer history as failed
     *
     * This status counts on "fail block".
     */
    public function failed(string $transferHistoryId): bool
    {
        $affected = TransferHistory::where('id', $transferHistoryId)->update([
            'status' => TransferHistory::STATUS_FAILED
        ]);

        return $affected == 1;
    }

    /**
     * List failed transfers from a user in the last seconds
     *
     * @param  string  $userId
     * @param  int  $seconds
     * @return \Illuminate\Support\Collection<\App\TransferHistory>
     */
    public function listFailed(string $userId, int $seconds): Collection
    {
        $past = Carbon::now()->subSeconds($seconds);

        $history = TransferHistory::where('user_id', $userId)
            ->where('event_at', '>', $past)
            ->where('status', '=', TransferHistory::STATUS_FAILED)
            ->get();

        return $history;
    }


}
