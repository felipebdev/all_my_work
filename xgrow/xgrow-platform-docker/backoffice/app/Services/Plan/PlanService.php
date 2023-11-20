<?php

namespace App\Services\Plan;

use App\Repositories\PlanRepository;
use App\Services\Objects\PlanFilter;

class PlanService
{
    /**
     * @var PlanRepository
     */
    private PlanRepository $plan;

    /**
     * @param PlanRepository $plan
     */
    public function __construct(PlanRepository $plan)
    {
        $this->plan = $plan;
    }

    /**
     * List plans
     * @return object
     */
    public function listPlans($inputs)
    {
        $platformId = $inputs['platform_id'] ?? null;
        $search = $inputs['search'] ?? null;

        $filter = (new PlanFilter())
            ->setSearch($search)
            ->setPlatformId($platformId);

        return $this->plan->listPlanPlatform($filter)
            ->select('plans.id',
                'plans.name',
                'platforms.name as platform_name')->get();
    }
}
