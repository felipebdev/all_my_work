<?php

namespace Modules\Integration\Services;

use Illuminate\Support\Collection;
use Modules\Integration\Events\PaymentData;

class MobileNotificationGenerator
{

    private MobileNotificationService $mobileNotificationService;

    public function __construct(MobileNotificationService $mobileNotificationService)
    {
        $this->mobileNotificationService = $mobileNotificationService;
    }

    /**
     * Expo notification is sent in a "batch" mode, it means that for two different messages we need two batches:
     * - one batch for message WITH plan name
     * - another batch for message WITHOUT plan name
     *
     * @param  string  $event
     * @param  string  $platformId
     * @param  array  $plansId
     * @param  \Modules\Integration\Events\PaymentData  $data
     * @return \Illuminate\Support\Collection
     */
    public function generateExpoNotifications(
        string $event,
        string $platformId,
        array $plansId,
        PaymentData $data
    ): Collection {
        $expoNotifications = new Collection();

        // handle case of messages WITH plan name
        $expoNotificationActionWithPlanName = $this->mobileNotificationService
            ->includesPlanName(true)
            ->generateExpoNotificationAction($event, $platformId, $plansId, $data->getPayment());

        if ($expoNotificationActionWithPlanName) {
            $expoNotifications->push($expoNotificationActionWithPlanName);
        }

        // handle case of messages WITHOUT plan name
        $expoNotificationActionWithoutPlanName = $this->mobileNotificationService
            ->includesPlanName(false)
            ->generateExpoNotificationAction($event, $platformId, $plansId, $data->getPayment());

        if ($expoNotificationActionWithoutPlanName) {
            $expoNotifications->push($expoNotificationActionWithoutPlanName);
        }

        // return the merged Expo notifications
        return $expoNotifications;
    }
}
