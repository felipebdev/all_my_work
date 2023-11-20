<?php

namespace Tests\Feature\Traits;

trait LocalDatabaseIds
{
    public string $platformId = '00000000-0000-0000-0000-000000000000';

    public int $salePlanId = 1;

    public int $subscriptionPlanId = 2;

    public int $secondarySubscriptionPlanId = 4;

    public array $orderBumps = [
        3,
    ];

    public int $upsell = 4;

    public int $platformUserId = 1; // platforms_users.id = 1

}
