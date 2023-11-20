<?php

namespace App\Repositories\Contracts;

use App\Services\Objects\PeriodFilter;
use App\Services\Objects\SaleReportFilter;
interface PaymentRepositoryInterface extends BaseRepositoryInterface
{
    public function update(array $where, array $data);
    public function batchUpdate(array $ids, array $data);

    public function allByOrderNumberAndStatus(
        string $orderNumber,
        array $status,
        array $columns = ['*']
    );

    /**
     * Get N records from specific id
     */
    public function getFromId(
        int $id,
        int $limit = 1,
        array $where = [],
        array $columns = ['*']
    );

    public function reportTransactionSale(
        string $platformId,
        SaleReportFilter $filters
    );

    /**
     * @deprecated Replaced by reportTransactionSale() on v0.23
     */
    public function reportSingleSale(
        string $platformId,
        SaleReportFilter $filters
    );

    public function reportSubscriberSale(
        string $platformId,
        SaleReportFilter $filters
    );

    public function reportNoLimitSale(
        string $platformId,
        SaleReportFilter $filters
    );

    public function totalTransactionsByStatus(
        PeriodFilter $filter,
        string $platformId = null
    );

    public function totalTransactions(
        PeriodFilter $filter,
        ?array $paymentsStatus = null,
        string $platformId = null
    );

    public function averageTicketPrice(
        PeriodFilter $filter,
        string $platformId = null
    );

    public function sumTransactions(
        PeriodFilter $filter = null,
        string $status = 'paid',
        string $platformId = null
    );

    public function sumTransactionsByType(
        PeriodFilter $filter = null,
        string $type,
        array $status = ['*'],
        string $platformId = null
    );

    public function totalBilling(
        PeriodFilter $filter,
        string $platformId = null
    );

    public function salesForecast(
        PeriodFilter $filter,
        string $platformId = null
    );

    public function percentTypePayment(
        PeriodFilter $filter,
        ?array $paymentsStatus = null,
        string $platformId = null
    );

    public function transactionSumByStatus(
        PeriodFilter $filter,
        string $platformId = null
    );

    public function transactionCountByStatus(
        PeriodFilter $filter,
        string $platformId = null
    );

    public function totalCardMultiples(
        PeriodFilter $filter,
        string $platformId = null
    );

    public function totalGeneratedVsPaid(
        PeriodFilter $filter,
        string $typePayment,
        string $platformId = null
    );

    public function graphTransactionByStatus(
        PeriodFilter $filter,
        string $platformId = null
    );

    public function graphCreditCardStatusTransactions(
        PeriodFilter $filter,
        string $platformId = null
    );

    public function graphTransactionsByInstallments(
        PeriodFilter $filter,
        string $platformId = null
    );

    public function transactionsDate(
        PeriodFilter $filter,
        string $status = null,
        string $platformId = null
    );

    public function totalAntecipationFees(
        string $status = 'paid',
        string $platformId = null
    );

    /**
     * Get all subscriber and plans payments
     */
    public function getBySubscriberAndPlansOnPeriod(
        string $subscriberId,
        array $plans,
        array $where = [],
        string $platformId = null,
        PeriodFilter $period = null,
        array $columns = ['*']
    );

    public function getTotalByAndTypesAndStatus(
        array $types,
        string $status,
        array $where = [],
        string $column = 'customer_value',
        string $platformId = null,
        SaleReportFilter $filters = null
    );
}
