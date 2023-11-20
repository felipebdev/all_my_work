<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface AuthorRepositoryInterface extends BaseRepositoryInterface
{

    /**
     * Get authors
     * @param string $platformId
     * @param ?string $searchTerm
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function list(string $platformId, ?string $searchTerm): Builder;

}
