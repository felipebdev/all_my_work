<?php

namespace App\Services\Reports;

use App\Jobs\Reports\Sales\Models\NoLimitSaleReport;
use App\Jobs\Reports\Sales\Models\SingleSaleReport;
use App\Jobs\Reports\Sales\Models\SubscriptionSaleReport;
use App\Jobs\Reports\Sales\Models\TransactionsReport;
use App\Jobs\Reports\Sales\SalesExportCSVReportQueue;
use App\Jobs\Reports\Sales\SalesExportXLSReportQueue;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Repositories\Contracts\RecurrenceRepositoryInterface;
use App\Services\Contracts\SaleReportServiceInterface;
use App\Services\Objects\SaleReportFilter;
use Exception;
use Illuminate\Support\Facades\Auth;

class SaleReportService implements SaleReportServiceInterface
{

    private $paymentRepository;
    private $recurrenceRepository;

    public function __construct(
        PaymentRepositoryInterface $paymentRepository,
        RecurrenceRepositoryInterface $recurrenceRepository
    ) {
        $this->paymentRepository = $paymentRepository;
        $this->recurrenceRepository = $recurrenceRepository;
    }

    public function getTransactionSaleReport(string $platformId, SaleReportFilter $filters)
    {
        return $this->paymentRepository->reportTransactionSale(
            $platformId,
            $filters
        );
    }

    /**
     * @deprecated Replaced by getTransactionSaleReport() on v0.23
     */
    public function getSingleSaleReport(string $platformId, SaleReportFilter $filters)
    {
        return $this->paymentRepository->reportSingleSale(
            $platformId,
            $filters
        );
    }

    public function getSubscriberSaleReport(string $platformId, SaleReportFilter $filters)
    {
        return $this->paymentRepository->reportSubscriberSale(
            $platformId,
            $filters
        );
    }

    public function getNoLimitSaleReport(string $platformId, SaleReportFilter $filters)
    {
        return $this->paymentRepository->reportNoLimitSale(
            $platformId,
            $filters
        );
    }

    public function exportReport(string $reportName, string $type, $user, SaleReportFilter $filters)
    {
        $report = null;
        switch ($reportName) {
            case 'single':
                $report = new SingleSaleReport();
                break;
            case 'subscription':
                $report = new SubscriptionSaleReport();
                break;
            case 'nolimit':
                $report = new NoLimitSaleReport();
                break;
            case 'transactions':
                $report = new TransactionsReport();
                break;
        }

        if (is_null($report)) {
            throw new Exception('Report type not found');
        }

        switch ($type) {
            case 'csv':
                SalesExportCSVReportQueue::dispatch(Auth::user(), $report, $filters);
                break;
            default:
                SalesExportXLSReportQueue::dispatch(Auth::user(), $report, $filters);
        }
    }

    public function getTotalByAndTypeAndStatus(
        array $types,
        string $status,
        array $where = null,
        string $column = null,
        string $platformId = null,
        SaleReportFilter $filters = null
    ) {
        return $this->paymentRepository->getTotalByAndTypesAndStatus(
            $types,
            $status,
            $where ?? [],
            $column ?? 'payment_plan.customer_value',
            $platformId,
            $filters
        );
    }

    public function getRecurrenceCountByPaymentStatus(
        string $status,
        array $where = null,
        string $platformId = null,
        SaleReportFilter $filters = null
    ) {
        return $this->recurrenceRepository->countByPaymentStatus($status, $where ?? [], $platformId, $filters);
    }

    /**
     * @deprecated Replaced by getMrr()
     */
    public function getRecurrenceTotalByPaymentStatus(
        string $status,
        array $where = null,
        string $column = null,
        string $platformId = null,
        SaleReportFilter $filters = null
    ) {
        return $this->recurrenceRepository->getTotalByPaymentStatus(
            $status,
            $where ?? [],
            $column ?? 'payment_plan.customer_value',
            $platformId,
            $filters
        );
    }

    /**
     * @inheritdoc
     */
    public function getMrr(string $platformId, SaleReportFilter $filters)
    {
        // MRR is based on customer value of all active subscriptions
        $where = ['subscriptions.status' => 'active'];
        $column = 'payments.customer_value';
        return $this->recurrenceRepository->getSubscriptionTotal($column, $where, $platformId, $filters);
    }
}
