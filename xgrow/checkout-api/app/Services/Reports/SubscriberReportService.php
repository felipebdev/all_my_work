<?php

namespace App\Services\Reports;

use App\Jobs\Reports\Subscribers\Models\SubscriberReport;
use App\Jobs\Reports\Subscribers\SubscribersExportCSVReportQueue;
use App\Jobs\Reports\Subscribers\SubscribersExportXLSReportQueue;
use App\Repositories\Contracts\SubscriberRepositoryInterface;
use App\Services\Contracts\SubscriberReportServiceInterface;
use App\Services\Objects\SubscriberReportFilter;
use Illuminate\Support\Facades\Auth;

class SubscriberReportService implements SubscriberReportServiceInterface {

    private $subscriberRepository;

    public function __construct(SubscriberRepositoryInterface $subscriberRepository) {
        $this->subscriberRepository = $subscriberRepository;
    }

    public function getSubscriberReport(string $platformId, SubscriberReportFilter $filters)
    {
        return $this->subscriberRepository->reportSubscriber(
            $platformId,
            $filters
        );
    }

    public function exportReport(string $reportName, string $type, $user, SubscriberReportFilter $filters)
    {
        $report = new SubscriberReport();
        switch($type) {
            case 'csv':
                SubscribersExportCSVReportQueue::dispatch(Auth::user(), $report, $filters);
                break;
            default:
                SubscribersExportXLSReportQueue::dispatch(Auth::user(), $report, $filters);
        }
    }
}
