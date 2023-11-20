<?php

namespace Modules\Integration\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Modules\Integration\Contracts\IActionRepository;
use Modules\Integration\Contracts\IActionService;
use Modules\Integration\Models\Integration;

class ActionService implements IActionService
{
    /**
     * @var IActionRepository
     */
    private $repository;

    public function __construct(IActionRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Integration $integration
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function allByIntegration(Integration $integration): Collection
    {
        return $this->repository->allByIntegrationWithPlans($integration->id);
    }

    /**
     * @param Integration $integration
     * @param array $data
     * @return Illuminate\Database\Eloquent\Model
     */
    public function store(Integration $integration, array $data): Model
    {
        return $this->repository->createAndSyncPlans(
            $integration->id,
            $data,
            $data['plans']
        );
    }

    /**
     * @param integer $id
     * @param array $data
     * @throws Illuminate\Database\Eloquent\ModelNotFoundException
     * @return Illuminate\Database\Eloquent\Model
     */
    public function update(int $id, array $data): Model
    {
        return $this->repository->updateAndSyncPlans(
            $id, 
            $data, 
            $data['plans']
        );
    }

    /**
     * @param integer $id
     * @return mixed
     */
    public function destroy(int $id)
    {
        return $this->repository->baseDelete($id);
    }
}
