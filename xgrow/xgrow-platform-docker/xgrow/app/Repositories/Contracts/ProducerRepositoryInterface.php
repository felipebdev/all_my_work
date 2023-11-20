<?php

namespace App\Repositories\Contracts;

use App\PlatformUser;
use App\Services\Objects\ProducerReportFilter;
use Illuminate\Database\Eloquent\Builder;

interface ProducerRepositoryInterface extends BaseRepositoryInterface
{

    /**
     * Generate producers query for given platform and filter options
     *
     * @param  string  $platformId
     * @param  \App\Services\Objects\ProducerReportFilter  $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function reportProducers(string $platformId, ProducerReportFilter $filters): Builder;

    /**
     * Get the platform user of a given producer ID
     *
     * @param  int  $producerId
     * @return \App\PlatformUser
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getPlatformUserByProducerId(int $producerId): PlatformUser;

}
