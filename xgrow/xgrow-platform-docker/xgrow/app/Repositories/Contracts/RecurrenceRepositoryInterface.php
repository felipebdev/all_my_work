<?php

namespace App\Repositories\Contracts;

use App\Services\Objects\SaleReportFilter;

interface RecurrenceRepositoryInterface extends BaseRepositoryInterface
{
    public function countByPaymentStatus(
        string $status,
        array $where = [],
        string $platformId = null,
        SaleReportFilter $filters = null
    );

    /**
     * @deprecated
     */
    public function getTotalByPaymentStatus(
        string $status,
        array $where = [],
        string $column = 'payments.customer_value',
        string $platformId = null,
        SaleReportFilter $filters = null
    );

    /**
     * Aggregate an specific column from recurrences (sum values)
     *
     * @param  string  $column  Column to be aggregated
     * @param  array  $where  Conditions
     * @param  string  $platformId
     * @param  \App\Services\Objects\SaleReportFilter  $filters
     * @return mixed
     */
    public function getSubscriptionTotal(string $column, array $where, string $platformId, SaleReportFilter $filters);
}
