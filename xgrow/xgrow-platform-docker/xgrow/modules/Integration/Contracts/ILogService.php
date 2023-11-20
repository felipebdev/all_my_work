<?php

namespace Modules\Integration\Contracts;

use Illuminate\Support\Collection;
use Modules\Integration\Models\Integration;

interface ILogService
{
    public function paginate(
        Integration $integration,
        int $page = 1, 
        int $limit = 50
    ): Collection;

    public function find($id);
}