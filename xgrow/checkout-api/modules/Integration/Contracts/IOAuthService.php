<?php

namespace Modules\Integration\Contracts;

interface IOAuthService
{
    public function save(
        IOAuthable $oAuth,
        string $platformId,
        string $code
    ): void;
}
