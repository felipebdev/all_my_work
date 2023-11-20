<?php

namespace App\Services\Contracts;

interface FinancialReportServiceInterface {
    public function totalTransactionsOnPeriod(
        string $startDate,
        string $endDate,
        string $format = 'Y-m-d',
        string $platformId = null
    );

    public function averageTicketPriceOnPeriod(
        string $startDate,
        string $endDate,
        string $format = 'Y-m-d',
        string $platformId = null
    );

    public function sumTransactionsOnPeriod(
        string $startDate,
        string $endDate,
        string $format = 'Y-m-d',
        string $status = 'paid',
        string $platformId = null
    );
    
    public function sumTransactionsByType(
        string $startDate,
        string $endDate,
        string $format = 'Y-m-d',
        string $type,
        array $status = ['*'],
        string $platformId = null
    );

    public function totalBilling(
        string $startDate,
        string $endDate,
        string $format = 'Y-m-d',
        string $platformId = null
    );

    public function percentTypePaymentOnPeriod(
        string $startDate,
        string $endDate,
        string $format = 'Y-m-d',
        ?array $paymentsStatus = null,
        string $platformId = null
    );

    public function transactionByStatusOnPeriod(
        string $startDate,
        string $endDate,
        string $format = 'Y-m-d',
        string $platformId = null
    );

    public function getTotalCardMultiples(
        string $startDate,
        string $endDate,
        string $format = 'Y-m-d',
        string $platformId = null
    );

    public function getGeneratedVsPaid(
        string $startDate,
        string $endDate,
        string $format = 'Y-m-d',
        string $typePayment,
        string $platformId = null
    );

    public function graphTransactionByStatusOnPeriod(
        string $startDate,
        string $endDate,
        string $format = 'Y-m-d',
        string $platformId = null
    );

    public function graphCreditCardStatusTransactionsOnPeriod(
        string $startDate,
        string $endDate,
        string $format = 'Y-m-d',
        string $platformId = null
    );

    public function graphTransactionsByInstallmentsOnPeriod(
        string $startDate,
        string $endDate,
        string $format = 'Y-m-d',
        string $platformId = null
    );

    public function transactionsDateOnPeriod(
        string $startDate,
        string $endDate,
        string $format = 'Y-m-d',
        string $status = null,
        string $platformId = null
    );

    public function totalAntecipationFees(
        string $status = 'paid',
        string $platformId = null
    );
}