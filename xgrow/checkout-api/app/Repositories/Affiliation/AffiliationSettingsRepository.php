<?php

namespace App\Repositories\Affiliation;

use App\AffiliationSettings;
use App\Exceptions\BadConfigurationException;
use App\Plan;

class AffiliationSettingsRepository
{

    /**
     * @param  string  $planId
     * @return \App\AffiliationSettings|null
     * @throws \App\Exceptions\BadConfigurationException
     */
    public function getAffiliationSettingsForPlan(string $planId): ?AffiliationSettings
    {
        $plan = Plan::findOrFail($planId);

        $product = $plan->product;

        $enabled = (bool) $product->affiliation_enabled;

        if (!$enabled) {
            return null;
        }

        $affiliationSettings = $product->affiliationSettings;

        if (!$affiliationSettings) {
            throw new BadConfigurationException('Affiliation settings not found for plan: '.$plan->id);
        }

        return $affiliationSettings;
    }

    public function isAffiliationEnabledForPlan(string $planId): bool
    {
        $plan = Plan::firstOrFail($planId);

        $product = $plan->product;

        return (bool) $product->affiliation_enabled;
    }

    public function setAffiliationSettingsForPlan(string $planId, array $settings)
    {
        $plan = Plan::findOrFail($planId);
        $product = $plan->product;

        $product->affiliation_enabled = $settings['enabled'] ?? false;
        $product->save();

        $affiliationSettings = AffiliationSettings::updateOrCreate(
            ['product_id' => $product->id],
            $settings
        );

        return $affiliationSettings;
    }

}
