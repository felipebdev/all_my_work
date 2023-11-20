<?php

namespace Modules\Integration\Contracts;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

interface IAppIntegrationRepository extends BaseRepositoryInterface
{
    /**
     * @param string $platformId
     * @param array $columns
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function allNotQueueableIntegrations(
        string $platformId,
        array $columns = ['*']
    ): Collection;
}
