<?php

namespace Modules\Integration\Contracts;

use Illuminate\Support\Collection;

interface ILogRepository 
{
    public function paginate(
        string $platformId,
        array $where = [],
        array $order = [],
        int $page = 1, 
        int $limit = 50
    ): Collection;

    public function find(string $id, string $platformId);
}