<?php

namespace App\Repositories\Finances;

use App\BankInformation;
use App\Client;
use App\Exceptions\Finances\RecipientNotFound;
use App\Platform;
use App\Producer;
use Illuminate\Support\Facades\Log;

class RecipientStatusRepository
{

    /**
     * @param  string  $recipientId
     * @param  string  $newStatus
     * @param  string|null  $statusReason
     * @return int
     */
    public function updateRecipientStatus(string $recipientId, string $newStatus, ?string $statusReason): int
    {
        $update = [
            'recipient_status' => $newStatus,
        ];

        if ($statusReason) {
            $update = array_merge($update, ['recipient_reason' => $statusReason]);
        }

        $affected = 0;
        $affected += Platform::query()->where('recipient_id', $recipientId)->update($update);
        $affected += Producer::query()->where('recipient_id', $recipientId)->update($update);
        $affected += BankInformation::query()->where('recipient_id', $recipientId)->update($update);

        return $affected;
    }

    /**
     * @param  string  $recipientId
     * @return string|null
     * @throws \App\Exceptions\Finances\RecipientNotFound
     */
    public function getCurrentRecipientStatus(string $recipientId): ?string
    {
        return $this->getModelByRecipientId($recipientId)->recipient_status ?? null;
    }

    /**
     * @param  string  $recipientId
     * @param  bool  $status
     * @throws \App\Exceptions\Finances\RecipientNotFound
     */
    public function upateClientVerifiedByRecipientId(string $recipientId, bool $status)
    {
        $email = $this->getEmailByRecipientId($recipientId);

        $this->updateClientVerifiedByEmail($email, $status);
    }

    /**
     * @param  string  $recipientId
     * @return string|null
     * @throws \App\Exceptions\Finances\RecipientNotFound
     */
    protected function getEmailByRecipientId(string $recipientId): ?string
    {
        $recipient = $this->getModelByRecipientId($recipientId);

        if ($recipient instanceof Platform) {
            return $recipient->client->email ?? null;
        } elseif ($recipient instanceof Producer) {
            return $recipient->platformUser->email ?? null;
        } elseif ($recipient instanceof BankInformation) {
            return $recipient->email ?? null;
        } else {
            Log::error('Unrecognized model from recipient', [
                'recipient' => $recipient,
            ]);
        }

        return null;
    }

    /**
     * @param  string  $email
     * @param  bool  $verified
     * @return int
     */
    protected function updateClientVerifiedByEmail(string $email, bool $verified): int
    {
        return Client::query()->where('email', $email)->update([
            'verified' => $verified,
        ]);
    }

    /**
     * @param  string  $recipientId
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object
     * @throws \App\Exceptions\Finances\RecipientNotFound
     */
    private function getModelByRecipientId(string $recipientId)
    {
        $platformRecipient = Platform::query()->where('recipient_id', $recipientId)->first();

        if ($platformRecipient) {
            return $platformRecipient;
        }

        $producerRecipient = Producer::query()->where('recipient_id', $recipientId)->first();

        if ($producerRecipient) {
            return $producerRecipient;
        }

        $bankInformationRecipient = BankInformation::query()->where('recipient_id', $recipientId)->first();

        if ($bankInformationRecipient) {
            return $bankInformationRecipient;
        }

        throw new RecipientNotFound('Recipient not found in database');
    }
}
