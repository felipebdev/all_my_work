<?php

namespace Modules\Integration\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Modules\Integration\Contracts\IActionRepository;
use Modules\Integration\Models\Action;

class ActionRepository extends BaseRepository implements IActionRepository
{
    public function model()
    {
        return Action::class;
    }

    /**
     * @param integer $integration
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function allByIntegrationWithPlans(int $integration): Collection
    {
        return $this->model
            ->with(['plans'])
            ->where('app_actions.app_id', '=', $integration)
            ->onPlatform()
            ->get();
    }

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
    ): Model {
        $data['app_id'] = $integration;
        $model = $this->baseCreate($data);
        $model->plans()->sync($plans);

        return $model;
    }

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
    ): Model {
        $model = $this->baseUpdate($id, $data);
        $model->plans()->sync($plans);

        return $model;
    }

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
    ): Collection {
        return $this->model
            ->select(
                'app_actions.id', 
                'app_actions.app_id',
                'app_actions.platform_id',
                'app_actions.event',
                'app_actions.action',
                'app_actions.metadata'
            )
            ->with([
                'integration' => function ($query) {
                    return $query->select(
                        'apps.id', 
                        'apps.type', 
                        'apps.api_key',
                        'apps.api_account',
                        'apps.api_webhook',
                        'apps.api_secret',
                        'apps.metadata'
                    );
                }
            ])
            ->where('app_actions.event', '=', $event)
            ->isActive()
            ->onPlatform($platformId)
            ->whereHas('integration', function ($query) {
                return $query->isActive();
            })
            ->whereHas('plans', function ($query) use ($plansId) {
                return $query->whereIn('plans.id', $plansId);
            })
            ->get();
    }

    /**
     * @param integer $id
     * @param string|null $platformId
     * @throws Illuminate\Database\Eloquent\ModelNotFoundException
     * @return Illuminate\Database\Eloquent\Model
     */
    public function findByIdWithIntegration(
        int $id,
        ?string $platformId
    ): Model {
        return $this->model
            ->select(
                'app_actions.id', 
                'app_actions.app_id',
                'app_actions.platform_id',
                'app_actions.event',
                'app_actions.action',
                'app_actions.metadata'
            )
            ->with([
                'integration' => function ($query) {
                    return $query->select(
                        'apps.id', 
                        'apps.type', 
                        'apps.api_key',
                        'apps.api_account',
                        'apps.api_webhook',
                        'apps.api_secret',
                        'apps.metadata'
                    );
                }
            ])
            ->isActive()
            ->onPlatform($platformId)
            ->findOrFail($id);
    }
}
