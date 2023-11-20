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
    
    public function getTotalByPaymentStatus(
        string $status,
        array $where = [],
        string $column = 'payments.customer_value',
        string $platformId = null, 
        SaleReportFilter $filters = null
    );
}