<?php

namespace Modules\Integration\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Modules\Integration\Contracts\ILogRepository;
use Modules\Integration\Contracts\ILogService;
use Modules\Integration\Models\Integration;

class LogService implements ILogService
{
    /**
     * @var Modules\Integration\Contracts\ILogRepository
     */
    private $repository;

    public function __construct(ILogRepository $repository)
    {
        $this->repository = $repository;
    }

    public function paginate(
        Integration $integration,
        int $page = 1,
        int $limit = 50
    ): Collection {
        return $this->repository->paginate(
            Auth::user()->platform_id,
            ['app_id' => $integration->id, 'service' => $integration->type],
            ['createdAt' => -1]
        );
    }

    public function find($id)
    {
        return $this->repository->find(
            $id,
            Auth::user()->platform_id
        );
    }
}
