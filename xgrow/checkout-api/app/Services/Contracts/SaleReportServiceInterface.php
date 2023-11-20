<?php

namespace App\Services\Contracts;

use App\Services\Objects\SaleReportFilter;

interface SaleReportServiceInterface {
    public function getTransactionSaleReport(
        string $platformId,
        SaleReportFilter $filters
    );

    /**
     * @deprecated Replaced by getTransactionSaleReport() on v0.23
     */
    public function getSingleSaleReport(
        string $platformId,
        SaleReportFilter $filters
    );

    public function getSubscriberSaleReport(
        string $platformId,
        SaleReportFilter $filters
    );

    public function getNoLimitSaleReport(
        string $platformId,
        SaleReportFilter $filters
    );

    public function exportReport(
        string $reportName,
        string $type,
        $user,
        SaleReportFilter $filters
    );

    public function getTotalByAndTypeAndStatus(
        array $types,
        string $status,
        array $where = null,
        string $column = null,
        string $platformId = null,
        SaleReportFilter $filters = null
    );

    public function getRecurrenceCountByPaymentStatus(
        string $status,
        array $where = null,
        string $platformId = null,
        SaleReportFilter $filters = null
    );

    public function getRecurrenceTotalByPaymentStatus(
        string $status,
        array $where = null,
        string $column = null,
        string $platformId = null,
        SaleReportFilter $filters = null
    );
}
