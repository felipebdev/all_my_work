<?php

namespace App\Http\Middleware\Purposes\Implementation;

use App\Http\Middleware\Purposes\Contracts\JwtPurposeStrategy;
use App\Platform;
use Illuminate\Http\Request;
use stdClass;

class ManualPayment implements JwtPurposeStrategy
{

    public function getErrors(stdClass $jwtPayload, Request $request): array
    {
        $errors = $this->getPayloadErrors($jwtPayload);
        if ($errors) {
            return $errors;
        }

        $errors = $this->getPlatformAccessErrors($jwtPayload->platform_id, $jwtPayload->user_id);
        if ($errors) {
            return $errors;
        }

        return [];
    }

    public function getPayloadErrors(stdClass $jwtPayload): array
    {
        $errors = [];

        if (empty($jwtPayload->platform_id)) {
            $errors[] = 'platform_id required';
        }

        if (empty($jwtPayload->user_id)) {
            $errors[] = 'user_id required';
        }

        return $errors;
    }

    private function getPlatformAccessErrors(string $platformId, string $userId): array
    {
        $hasAccess = Platform::checkPermission($platformId, $userId, 'financial');
        if (!$hasAccess) {
            return ['User can not access this platform'];
        }

        return [];
    }
}
