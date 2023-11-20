<?php

namespace App\Http\Middleware\Purposes\Implementation;

use App\Http\Middleware\Purposes\Contracts\JwtPurposeStrategy;
use App\Platform;
use Illuminate\Http\Request;
use stdClass;

class Recipient implements JwtPurposeStrategy
{

    protected array $actors = [
        'client',
        'producer',
        'affiliate'
    ];

    protected array $producerTypes = [
        'producer',
        'affiliate',
    ];

    public function getErrors(stdClass $jwtPayload, Request $request): array
    {
        $errors = $this->getPayloadErrors($jwtPayload);
        if ($errors) {
            return $errors;
        }

        $errors = $this->getPlatformAccessErrors(
            $jwtPayload->platform_id,
            $jwtPayload->user_id,
            $jwtPayload->acting_as
        );

        if ($errors) {
            return $errors;
        }

        return [];
    }

    private function getPayloadErrors(stdClass $payload): array
    {
        $errors = [];

        if (empty($payload->platform_id)) {
            $errors[] = 'platform_id required';
        }

        if (empty($payload->user_id)) {
            $errors[] = 'user_id required';
        }

        $actingAs = $payload->acting_as ?? null;
        if (!in_array($actingAs, $this->actors)) {
            $errors[] = 'acting_as required and must be one of: '.implode(', ', $this->actors);
        }

        return $errors;
    }

    private function getPlatformAccessErrors(string $platformId, string $userId, string $actingAs): array
    {
        if (in_array($actingAs, $this->producerTypes)) {
            return $this->getProducerAccessErrors($platformId, $userId);
        }

        return $this->getClientAccessErrors($platformId, $userId);
    }

    private function getClientAccessErrors(string $platformId, string $userId): array
    {
        $hasAccess = Platform::checkPermission($platformId, $userId, 'financial');
        if (!$hasAccess) {
            return ['User can not access this platform'];
        }

        return [];
    }

    private function getProducerAccessErrors(string $platformId, string $userId): array
    {
        $hasAccess = Platform::checkProducerPermission($platformId, $userId);
        if (!$hasAccess) {
            return ['Producer/Affiliate can not access this platform'];
        }

        return [];
    }

}
