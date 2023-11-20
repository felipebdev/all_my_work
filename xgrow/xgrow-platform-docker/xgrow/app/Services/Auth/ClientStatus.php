<?php

namespace App\Services\Auth;

use App\Client;
use App\Platform;
use App\Services\Auth\Objects\ClientStatusResult;

class ClientStatus
{

    public static function withoutPlatform(string $email): ClientStatusResult
    {
        $client = Client::where('email', $email)->first();

        if (!$client) {
            return ClientStatusResult::notFound();
        }

        $clientApproved = $client->verified === 1;

        return ClientStatusResult::foundWithoutPlatform($clientApproved);
    }

    public static function withPlatform(string $platformId, string $email): ClientStatusResult
    {
        /* Verify if user is client */
        $client = Client::where('email', $email)->first();

        if (!$client) {
            return ClientStatusResult::notFound();
        }

        $clientApproved = $client->verified === 1;

        $platformOwner = Platform::where('customer_id', $client->id)
            ->where('id', $platformId)
            ->first();

        $isOwner = !empty($platformOwner);

        $recipientStatusMessage = $platformOwner->recipient_reason ?? '';

        return ClientStatusResult::foundWithPlatform($clientApproved, $isOwner, $recipientStatusMessage);
    }
}
