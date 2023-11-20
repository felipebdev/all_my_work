<?php

namespace Modules\Integration\Contracts;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface IActionRepository extends BaseRepositoryInterface
{
    /**
     * @param integer $integration
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function allByIntegrationWithPlans(int $integration): Collection;

    /**
     * @param integer $integration
     * @param array $data
     * @param array $plans
     * @return Illuminate\Database\Eloquent\Model
     */
    public function createAndSyncPlans(
        int $integration,
        array $data = [],
        array $plans = []
    ): Model;

    /**
     * @param integer $id
     * @param array $data
     * @param array $plans
     * @throws Illuminate\Database\Eloquent\ModelNotFoundException;
     * @return Illuminate\Database\Eloquent\Model
     */
    public function updateAndSyncPlans(
        int $id,
        array $data = [],
        array $plans = []
    ): Model;

   /**
     * Get all active app_actions with active apps 
     * 
     * @param string $event
     * @param array $plansId
     * @param string $platformId
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function allByEventWithIntegration(
        string $event,
        array $plansId,
        ?string $platformId
    ): Collection;

    /**
     * @param integer $id
     * @param string|null $platformId
     * @throws Illuminate\Database\Eloquent\ModelNotFoundException
     * @return Illuminate\Database\Eloquent\Model
     */
    public function findByIdWithIntegration(
        int $id,
        ?string $platformId
    ): Model;
}
