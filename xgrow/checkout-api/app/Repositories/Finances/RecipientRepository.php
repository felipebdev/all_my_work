<?php

namespace App\Repositories\Finances;

use App\Client;
use App\Exceptions\Finances\RecipientNotFound;
use App\Platform;
use App\Producer;
use Exception;
use stdClass;

class RecipientRepository
{

    public const RECIPIENT_TYPE_CLIENT = 'client';
    public const RECIPIENT_TYPE_PRODUCER = 'producer';
    public const RECIPIENT_TYPE_AFFILIATE = 'affiliate';
    public const RECIPIENT_TYPE_BANK_INFORMATION = 'bank_information';

    /**
     * Get recipient id based on platform and user id.
     *
     * Recipient can be a Producer (`client`), a Co-Producer (`producer`), an Affiliate (`affiliate`).
     *
     * @param  string  $platformId
     * @param  string  $userId
     * @param  string|null  $actingAs
     * @return \stdClass
     * @throws \App\Exceptions\Finances\RecipientNotFound
     */
    public function getRecipientIdWithType(string $platformId, string $userId, ?string $actingAs = null): stdClass
    {
        if ($actingAs) {
            return $this->getRecipientInfoByActor($platformId, $userId, $actingAs);
        }

        return $this->locateAnyRecipient($platformId, $userId);
    }

    /**
     * Get recipient id based on platform, user id and actor
     *
     * @param  string  $platformId
     * @param  string  $userId
     * @param  string  $actingAs
     * @return \stdClass
     * @throws \App\Exceptions\Finances\RecipientNotFound
     */
    public function getRecipientInfoByActor(string $platformId, string $userId, string $actingAs): stdClass
    {
        if ($actingAs === self::RECIPIENT_TYPE_CLIENT) {
            return $this->locateClientRecipient($platformId, $userId);
        } elseif ($actingAs === self::RECIPIENT_TYPE_PRODUCER) {
            return $this->locateProducerRecipient($platformId, $userId);
        } elseif ($actingAs === self::RECIPIENT_TYPE_AFFILIATE) {
            return $this->locateAffiliateRecipient($platformId, $userId);
        }

        throw new Exception('Invalid recipient Type');
    }

    /**
     * @param  string  $platformId
     * @param  string  $userId
     * @return object|null
     * @throws \App\Exceptions\Finances\RecipientNotFound
     */
    private function locateClientRecipient(string $platformId, string $userId): ?object
    {
        $hasAccess = Platform::checkPermission($platformId, $userId, 'financial');
        if (!$hasAccess) {
            throw new RecipientNotFound('Recipient not found for this platform');
        }

        $platform = Platform::findOrFail($platformId);

        $recipientId = $platform->recipient_id ?? Client::where('id', $platform->customer_id)->first()->recipient_id;
        if (!$recipientId) {
            throw new RecipientNotFound('Recipient not found for this platform');
        }

        return (object) [
            'id' => $recipientId,
            'type' => self::RECIPIENT_TYPE_CLIENT,
        ];
    }

    /**
     * @param  string  $platformId
     * @param  string  $userId
     * @return object|null
     * @throws \App\Exceptions\Finances\RecipientNotFound
     */
    private function locateProducerRecipient(string $platformId, string $userId): ?object
    {
        $producerRecipient = Producer::where('platform_id', $platformId)
            ->where('platform_user_id', $userId)
            ->where('type', Producer::TYPE_PRODUCER)
            ->first();

        $recipientId = $producerRecipient->recipient_id ?? null;
        if (!$recipientId) {
            throw new RecipientNotFound('Recipient not found for this platform');
        }

        return (object) [
            'id' => $recipientId,
            'type' => self::RECIPIENT_TYPE_PRODUCER,
        ];
    }

    private function locateAffiliateRecipient(string $platformId, string $userId): ?object
    {
        $affiliateRecipient = Producer::where('platform_id', $platformId)
            ->where('platform_user_id', $userId)
            ->where('type', Producer::TYPE_AFFILIATE)
            ->first();

        $recipientId = $affiliateRecipient->recipient_id ?? null;
        if (!$recipientId) {
            throw new RecipientNotFound('Recipient not found for this platform');
        }

        return (object) [
            'id' => $recipientId,
            'type' => self::RECIPIENT_TYPE_PRODUCER,
        ];
    }

    /**
     * @param  string  $platformId
     * @param  string  $userId
     * @return object
     * @throws \App\Exceptions\Finances\RecipientNotFound
     */
    public function locateAnyRecipient(string $platformId, string $userId): object
    {
        $recipients = [];

        try {
            $recipient = $this->locateClientRecipient($platformId, $userId);;
            if ($recipient) {
                $recipients[] = $recipient;
            }
        } catch (RecipientNotFound $e) {
            // ignore client exception
        }

        try {
            $recipient = $this->locateProducerRecipient($platformId, $userId);
            if ($recipient) {
                return $recipient;
            }
        } catch (RecipientNotFound $e) {
            // ignore producer exception
        }

        try {
            $recipient = $this->locateAffiliateRecipient($platformId, $userId);
            if ($recipient) {
                return $recipient;
            }
        } catch (RecipientNotFound $e) {
            // ignore affiliate exception
        }

        throw new RecipientNotFound('Recipient not found');
    }

}
