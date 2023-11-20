<?php

namespace App\Repositories\Integrations;

use App\Integration;
use Illuminate\Support\Str;
use App\Repositories\Contracts\IntegrationRepositoryInterface;
class IntegrationRepository implements IntegrationRepositoryInterface {
    public function findActiveByPlatformAndTrigger(string $platformId, string $trigger) {
        $trigger = Str::snake($trigger);
        return Integration::where('platform_id', $platformId)
            ->where('source_token', 'like', "%{$trigger}%")
            ->where('flag_enable', 1)
            ->get();
    }
}