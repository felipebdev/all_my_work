<?php

namespace App\Services\Reports;

use App\Jobs\Reports\Producers\ProducersExportCSVReportQueue;
use App\Jobs\Reports\Producers\ProducersExportXLSReportQueue;
use App\PlatformUser;
use App\Repositories\Contracts\ProducerRepositoryInterface;
use App\Services\Contracts\ProducerReportServiceInterface;
use App\Services\Objects\ProducerReportFilter;
use Illuminate\Database\Eloquent\Builder;

class ProducerReportService implements ProducerReportServiceInterface
{

    private ProducerRepositoryInterface $producerRepository;

    public function __construct(ProducerRepositoryInterface $producerRepository)
    {
        $this->producerRepository = $producerRepository;
    }

    public function getProducersReport(string $platformId, ProducerReportFilter $filters): Builder
    {
        return $this->producerRepository->reportProducers($platformId, $filters);
    }

    public function exportReport(string $platformId, PlatformUser $user, string $type, ProducerReportFilter $filters)
    {
        switch ($type) {
            case 'csv':
                ProducersExportCSVReportQueue::dispatch($platformId, $user->id, $filters);
                break;
            default:
                ProducersExportXLSReportQueue::dispatch($platformId, $user->id, $filters);
        }
    }
}
