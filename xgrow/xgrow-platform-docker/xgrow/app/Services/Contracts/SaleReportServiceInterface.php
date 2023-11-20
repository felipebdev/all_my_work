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

    /**
     * @deprecated Replaced by getMrr()
     */
    public function getRecurrenceTotalByPaymentStatus(
        string $status,
        array $where = null,
        string $column = null,
        string $platformId = null,
        SaleReportFilter $filters = null
    );

    /**
     * Get MRR of a given platform (with filters).
     *
     * @param  string  $platformId
     * @param  \App\Services\Objects\SaleReportFilter  $filters
     * @return mixed
     */
    public function getMrr(string $platformId, SaleReportFilter $filters);

}
