<?php

namespace App\Services\Contracts;

use App\Services\Objects\LeadReportFilter;

interface LeadReportServiceInterface {
    public function getLeadsReport(
        string $platformId,
        LeadReportFilter $filters
    );

    public function exportLeadReport(
        string $reportName,
        string $type,
        $user,
        LeadReportFilter $filters
    );
}
