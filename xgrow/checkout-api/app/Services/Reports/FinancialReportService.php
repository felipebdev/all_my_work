<?php

namespace App\Services\Reports;

use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Services\Contracts\FinancialReportServiceInterface;
use App\Services\Objects\PeriodFilter;
use stdClass;

class FinancialReportService implements FinancialReportServiceInterface 
{
    private $paymentRepository;

    public function __construct(PaymentRepositoryInterface $paymentRepository) {
        $this->paymentRepository = $paymentRepository;
    }

    public function totalTransactionsOnPeriod(
        string $startDate,
        string $endDate,
        string $format = 'Y-m-d',
        string $platformId = null
    ) {
        $period = new PeriodFilter(
            $startDate,
            $endDate,
            $format
        );

        return $this->paymentRepository
            ->totalTransactionsByStatus($period, $platformId);
    }

    public function averageTicketPriceOnPeriod(
        string $startDate,
        string $endDate,
        string $format = 'Y-m-d',
        string $platformId = null
    ) {
        $period = new PeriodFilter(
            $startDate,
            $endDate,
            $format
        );

        return $this->paymentRepository
            ->averageTicketPrice($period, $platformId);
    }

    public function sumTransactionsOnPeriod(
        string $startDate,
        string $endDate,
        string $format = 'Y-m-d',
        string $status = 'paid',
        string $platformId = null
    ) {
        $period = null;
        if (!empty($startDate) && !empty($endDate)) {
            $period = new PeriodFilter(
                $startDate,
                $endDate,
                $format
            );
        }

        return $this->paymentRepository
            ->sumTransactions($period, $status, $platformId);
    }

    public function sumTransactionsByType(
        string $startDate,
        string $endDate,
        string $format = 'Y-m-d',
        string $type,
        array $status = ['*'],
        string $platformId = null
    ) {
        $period = new PeriodFilter(
            $startDate,
            $endDate,
            $format
        );

        return $this->paymentRepository->sumTransactionsByType(
            $period, 
            $type,  
            $status,
            $platformId
        );
    }

    public function totalBilling(
        string $startDate,
        string $endDate,
        string $format = 'Y-m-d',
        string $platformId = null
    ) {
        $period = new PeriodFilter(
            $startDate,
            $endDate,
            $format
        );

        return $this->paymentRepository
            ->totalBilling($period, $platformId);
    }

    public function percentTypePaymentOnPeriod(
        string $startDate,
        string $endDate,
        string $format = 'Y-m-d',
        ?array $paymentsStatus = null,
        string $platformId = null
    ) {
        $period = new PeriodFilter(
            $startDate,
            $endDate,
            $format
        );

        return $this->paymentRepository
            ->percentTypePayment($period, $paymentsStatus, $platformId);
    }

    public function transactionByStatusOnPeriod(
        string $startDate,
        string $endDate,
        string $format = 'Y-m-d',
        string $platformId = null
    ) {
        $period = new PeriodFilter(
            $startDate,
            $endDate,
            $format
        );

        $sum = $this->paymentRepository
            ->transactionSumByStatus($period, $platformId)
            ->toArray();

        $count = $this->paymentRepository
            ->transactionCountByStatus($period, $platformId)
            ->toArray();

        $transactions = [];
        foreach ($sum as $value) {
            $transaction = new stdClass();
            $transaction->status = $value['status'];
            $transaction->total = $value['total'];
            $transaction->count = 0;

            $key = array_search($value['status'], array_column($count, 'status'));
            if (!is_bool($key)) {
                $transaction->count = $count[$key]['count'];
            }

            $transactions[] = $transaction;
        }

        return $transactions;
    }

    public function getTotalCardMultiples(
        string $startDate,
        string $endDate,
        string $format = 'Y-m-d',
        string $platformId = null
    ) {
        $period = new PeriodFilter(
            $startDate,
            $endDate,
            $format
        );

        return $this->paymentRepository
            ->totalCardMultiples($period, $platformId);
    }

    public function getGeneratedVsPaid(
        string $startDate,
        string $endDate,
        string $format = 'Y-m-d',
        string $typePayment,
        string $platformId = null
    ) {
        $period = new PeriodFilter(
            $startDate,
            $endDate,
            $format
        );

        return $this->paymentRepository
            ->totalGeneratedVsPaid($period, $typePayment, $platformId);
    }

    public function graphTransactionByStatusOnPeriod(
        string $startDate,
        string $endDate,
        string $format = 'Y-m-d',
        string $platformId = null
    ) {
        $period = new PeriodFilter(
            $startDate,
            $endDate,
            $format
        );

        return $this->paymentRepository
            ->graphTransactionByStatus($period, $platformId);
    }

    public function graphCreditCardStatusTransactionsOnPeriod(
        string $startDate,
        string $endDate,
        string $format = 'Y-m-d',
        string $platformId = null
    ) {
        $period = new PeriodFilter(
            $startDate,
            $endDate,
            $format
        );

        return $this->paymentRepository
            ->graphCreditCardStatusTransactions($period, $platformId);
    }

    public function graphTransactionsByInstallmentsOnPeriod(
        string $startDate,
        string $endDate,
        string $format = 'Y-m-d',
        string $platformId = null
    ) {
        $period = new PeriodFilter(
            $startDate,
            $endDate,
            $format
        );

        return $this->paymentRepository
            ->graphTransactionsByInstallments($period, $platformId);
    }

    public function transactionsDateOnPeriod(
        string $startDate,
        string $endDate,
        string $format = 'Y-m-d',
        string $status = null,
        string $platformId = null
    ) {
        $period = new PeriodFilter(
            $startDate,
            $endDate,
            $format
        );

        return $this->paymentRepository
            ->transactionsDate($period, $status, $platformId);
    }

    public function totalAntecipationFees(
        string $status = 'paid',
        string $platformId = null
    ) {
        return $this->paymentRepository
            ->totalAntecipationFees($status, $platformId);
    }
}