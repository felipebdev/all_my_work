<?php

namespace Modules\Integration\Repositories;

use App\Constants;
use App\Integration as NotQueueableIntegration;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Modules\Integration\Contracts\IAppIntegrationRepository;
use Modules\Integration\Models\Integration;

class AppIntegrationRepository extends BaseRepository implements IAppIntegrationRepository
{
    const NOT_QUEUEABLE_INTEGRATIONS = [
        Constants::CONSTANT_INTEGRATION_DIGITALMANAGERGURU,
        Constants::CONSTANT_INTEGRATION_EDUZZ,
        Constants::CONSTANT_INTEGRATION_FACEBOOKPIXEL,
        Constants::CONSTANT_INTEGRATION_GOOGLEADS,
        Constants::CONSTANT_INTEGRATION_HOTMART,
        Constants::CONSTANT_INTEGRATION_PANDAVIDEO
    ];

    public function model()
    {
        return Integration::class;
    }

    /**
     * @param string $platformId
     * @param array $columns
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function allNotQueueableIntegrations(
        string $platformId,
        array $columns = ['*']
    ): Collection {
        return NotQueueableIntegration::select($columns)
            ->whereIn('integrations.id_integration', self::NOT_QUEUEABLE_INTEGRATIONS)
            ->where('integrations.platform_id', '=', $platformId)
            ->get();
    }
}
