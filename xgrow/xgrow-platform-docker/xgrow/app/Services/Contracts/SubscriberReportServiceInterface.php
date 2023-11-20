<?php

namespace App\Services\Contracts;

use App\Services\Objects\SubscriberReportFilter;

interface SubscriberReportServiceInterface {
    public function getSubscriberReport(
        string $platformId,
        SubscriberReportFilter $filters
    );

    public function exportReport(
        string $reportName,
        string $type,
        $user,
        SubscriberReportFilter $filters
    );
}
