<?php

namespace App\Repositories\Contracts;

interface IntegrationRepositoryInterface
{
    public function findActiveByPlatformAndTrigger(string $platformId, string $trigger);
}
