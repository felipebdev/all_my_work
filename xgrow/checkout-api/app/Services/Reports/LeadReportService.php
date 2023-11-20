<?php

namespace App\Services\Reports;

use App\Jobs\Reports\Leads\Models\LeadReport;
use App\Jobs\Reports\Leads\LeadsExportCSVReportQueue;
use App\Jobs\Reports\Leads\LeadsExportXLSReportQueue;
use App\Repositories\Contracts\LeadRepositoryInterface;
use App\Services\Contracts\LeadReportServiceInterface;
use App\Services\Objects\LeadReportFilter;
use Illuminate\Support\Facades\Auth;

class LeadReportService implements LeadReportServiceInterface
{

    private $leadRepository;

    public function __construct(LeadRepositoryInterface $leadRepository)
    {
        $this->leadRepository = $leadRepository;
    }

    public function getLeadsReport(string $platformId, LeadReportFilter $filters)
    {
        return $this->leadRepository->reportLead(
            $platformId,
            $filters
        );
    }

    public function exportLeadReport(string $reportName, string $type, $user, LeadReportFilter $filters)
    {
        $report = new LeadReport();
        switch ($type) {
            case 'csv':
                LeadsExportCSVReportQueue::dispatch(Auth::user(), $report, $filters);
                break;
            default:
                LeadsExportXLSReportQueue::dispatch(Auth::user(), $report, $filters);
        }
    }
}
