<?php

namespace App\Services\Affiliation;

use App\AffiliationSettings;
use App\Plan;
use App\Repositories\Affiliation\AffiliationSettingsRepository;
use Exception;

class AffiliationService
{
    private AffiliationSettingsRepository $affiliationSettingsRepository;

    public function __construct(AffiliationSettingsRepository $affiliationSettingsRepository)
    {
        $this->affiliationSettingsRepository = $affiliationSettingsRepository;
    }

    /**
     * @param  string  $platformId
     * @param  string  $planId
     * @return \App\AffiliationSettings
     * @throws \App\Exceptions\BadConfigurationException
     */
    public function getAffiliationSettings(string $platformId, string $planId): ?AffiliationSettings
    {
        if (!$this->planBelongsToPlatform($platformId, $planId)) {
            throw new Exception('Plano não pertence à plataforma');
        }

        return $this->affiliationSettingsRepository->getAffiliationSettingsForPlan($planId);
    }

    private function planBelongsToPlatform(string $platformId, string $planId): bool
    {
        $plan = Plan::findOrFail($planId);

        $planPlatform = $plan->platform_id;

        if ($platformId != $planPlatform) {
            return false;
        }

        return true;
    }
}
