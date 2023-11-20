<?php

namespace App\Repositories\Contracts;
use App\Services\Objects\LeadReportFilter;

interface LeadRepositoryInterface
{
    public function reportLead(
        string $platformId,
        LeadReportFilter $filters
    );
}
