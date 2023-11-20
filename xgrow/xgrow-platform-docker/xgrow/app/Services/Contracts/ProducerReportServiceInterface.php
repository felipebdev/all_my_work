<?php

namespace App\Services\Contracts;

use App\PlatformUser;
use App\Services\Objects\ProducerReportFilter;
use Illuminate\Database\Eloquent\Builder;

interface ProducerReportServiceInterface
{

    public function getProducersReport(string $platformId, ProducerReportFilter $filters): Builder;

    /**
     * Export report in the given format
     *
     * @param  string  $platformId
     * @param  \App\PlatformUser  $user
     * @param  string  $type 'csv' and 'xlsx' supported
     * @param  \App\Services\Objects\ProducerReportFilter  $filters
     * @return mixed
     */
    public function exportReport(string $platformId, PlatformUser $user, string $type, ProducerReportFilter $filters);

}
