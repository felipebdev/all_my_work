<?php

namespace Modules\Integration\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Modules\Integration\Contracts\IAppIntegrationRepository;
use Modules\Integration\Contracts\IAppIntegrationService;

class AppIntegrationService implements IAppIntegrationService
{
    /**
     * @var IAppIntegrationRepository
     */
    private $repository;

    public function __construct(IAppIntegrationRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param string $platformId
     * @return array
     */
    public function all(string $platformId): array 
    {
        $apps = $this->repository->baseFindWhere(
            ['apps.platform_id' => $platformId],
            ['id', 'is_active', 'description', 'type']
        );

        $notQueueableApps = $this->repository->allNotQueueableIntegrations(
            $platformId,
            ['id', 'flag_enable as is_active', 'name_integration as description', 'id_integration as type']
        );
        
        $integrations = $apps->merge($notQueueableApps)->all();
        usort($integrations, function ($a, $b) { return strcmp(strtolower($a->type), strtolower($b->type)); }); //alphabetical sort

        return $integrations;
    }

    /**
     * @param array $data
     * @return Illuminate\Database\Eloquent\Model
     */
    public function store(array $data): Model
    {
        return $this->repository->baseCreate($data);
    }

    /**
     * @param integer $id
     * @param array $data
     * @throws Illuminate\Database\Eloquent\ModelNotFoundException
     * @return Illuminate\Database\Eloquent\Model
     */
    public function update(int $id, array $data): Model
    {
        return $this->repository->baseUpdate($id, $data);
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
