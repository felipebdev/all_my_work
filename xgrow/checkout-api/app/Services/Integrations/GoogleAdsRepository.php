<?php

namespace App\Services\Integrations;

use App\Integration as LegacyIntegration;
use Modules\Integration\Models\Integration;

class GoogleAdsRepository
{

    /**
     * Load Google Ads info from the database.
     *
     * @note This method can return a newer Integration or a legacy Integration.
     * @param  string  $platformId
     * @return mixed|null
     */
    public function loadGoogleAdsFromDatabase(string $platformId)
    {
        // try with new Integration model
        $integration = Integration::where('platform_id', $platformId)
            ->where('type', 'googleads') // @ToDo update and use TypeEnum
            ->where('is_active', 1)
            ->first();

        if ($integration) {
            return $integration;
        }

        // try with legacy Integration model
        $legacyIntegration = LegacyIntegration::where('platform_id', $platformId)
            ->where('id_integration', 'GOOGLEADS')
            ->where('flag_enable', 1)
            ->first();

        return $legacyIntegration;
    }
}
