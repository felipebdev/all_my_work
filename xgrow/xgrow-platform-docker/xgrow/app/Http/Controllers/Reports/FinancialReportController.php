<?php

namespace App\Http\Controllers\Reports;

use App\CreditCard;
use App\Enums\TransactionResponseEnum;
use App\Http\Controllers\Controller;
use App\Http\Traits\CustomResponseTrait;
use App\Payment;
use App\Services\Contracts\FinancialReportServiceInterface;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FinancialReportController extends Controller
{
    private $financialReportService;

    use CustomResponseTrait;

    public function __construct(FinancialReportServiceInterface $financialReportService)
    {
        $this->financialReportService = $financialReportService;
    }

    public function index(Request $request)
    {
        $start = Carbon::now()->subtract(30, 'day')->format('d/m/Y');
        $end = Carbon::now()->format('d/m/Y');
        $search = ['period' => "{$start} - {$end}"];

        return view('reports.financial.index', compact('search'));
    }

    public function indexNext()
    {
        return view('reports.financial.index-next');
    }

    private function convertPeriod($period)
    {
        $periods = explode(' - ', $period);
        $startPeriod = $periods[0];
        $startPeriod = explode('/', $startPeriod);
        $endPeriod = $periods[1];
        $endPeriod = explode('/', $endPeriod);
        return [$startPeriod[2] . '-' . $startPeriod[1] . '-' . $startPeriod[0] . ' 00:00:00', $endPeriod[2] . '-' . $endPeriod[1] . '-' . $endPeriod[0] . ' 23:59:59'];
    }

    public function getTotalTransactions(Request $request)
    {
        $periods = explode(' - ', $request->period);
        $data = $this->financialReportService->totalTransactionsOnPeriod(
            $periods[0],
            $periods[1],
            'd/m/Y'
        );

        return response()->json(['data' => $data], 200);
    }

    public function getSalesForecast(Request $request)
    {
        $platformId = Auth::user()->platform_id;
        [$begin, $end] = explode(' - ', $request->period);
        $data = $this->financialReportService->salesForecast($begin, $end, 'd/m/Y', $platformId);
        return response()->json(['data' => $data]);
    }

    public function getAverageTicketPrice(Request $request)
    {
        $periods = explode(' - ', $request->period);
        $total = $this->financialReportService->averageTicketPriceOnPeriod(
            $periods[0],
            $periods[1],
            'd/m/Y'
        );

        return response()->json(['data' => ['total' => $total]], 200);
    }

    public function getSumTransactions(Request $request)
    {
        $startDate = '';
        $endDate = '';
        if ($request->has('period')) {
            $periods = explode(' - ', $request->period);
            $startDate = $periods[0];
            $endDate = $periods[1];
        }

        $total = $this->financialReportService->sumTransactionsOnPeriod(
            $startDate,
            $endDate,
            'd/m/Y',
            $request->status ?? 'paid'
        );

        return response()->json(['data' => ['total' => $total]], 200);
    }

    public function getPercentTypePayment(Request $request)
    {
        $periods = explode(' - ', $request->period);
        $typePayments = $this->financialReportService->percentTypePaymentOnPeriod(
            $periods[0],
            $periods[1],
            'd/m/Y',
            $request->status
        );

        return response()->json(['data' => $typePayments], 200);
    }

    public function getTransactionByStatus(Request $request)
    {
        $periods = explode(' - ', $request->period);
        $transactions = $this->financialReportService->transactionByStatusOnPeriod(
            $periods[0],
            $periods[1],
            'd/m/Y'
        );

        return response()->json(['data' => $transactions], 200);
    }

    public function getTotalCardMultiples(Request $request)
    {
        $periods = explode(' - ', $request->period);
        $transactions = $this->financialReportService->getTotalCardMultiples(
            $periods[0],
            $periods[1],
            'd/m/Y'
        );

        return response()->json(['data' => $transactions], 200);
    }

    public function getGeneratedVsPaid(Request $request)
    {
        $periods = explode(' - ', $request->period);
        $transactions = $this->financialReportService->getGeneratedVsPaid(
            $periods[0],
            $periods[1],
            'd/m/Y',
            $request->type_payment ?? 'pix'
        );

        return response()->json(['data' => $transactions], 200);
    }

    public function getToReceive(Request $request)
    {
        $periods = explode(' - ', $request->period);
        $total = $this->financialReportService->sumTransactionsByType(
            $periods[0],
            $periods[1],
            'd/m/Y',
            Payment::TYPE_UNLIMITED,
            [Payment::STATUS_PENDING, Payment::STATUS_FAILED],
        );

        return response()->json(['data' => ['total' => $total]], 200);
    }

    public function getTotalBilling(Request $request)
    {
        $periods = explode(' - ', $request->period);
        $total = $this->financialReportService->totalBilling(
            $periods[0],
            $periods[1],
            'd/m/Y'
        );

        return response()->json(['data' => ['total' => $total]], 200);
    }

    /**
     * TODO: save card brand in payment table => update this kpi
     */
    public function getCreditCardBrands(Request $request)
    {
        $total = CreditCard::join('subscribers', 'subscribers.id', 'credit_cards.subscriber_id')
            ->where('subscribers.platform_id', Auth::user()->platform_id)
            ->count() ?? 0;

        $brands = CreditCard::select(
            DB::raw("credit_cards.brand, COUNT(credit_cards.id) AS count")
        )
            ->join('subscribers', 'subscribers.id', 'credit_cards.subscriber_id')
            ->where('subscribers.platform_id', Auth::user()->platform_id)
            ->groupBy('credit_cards.brand')
            ->get();

        return response()->json(['data' => ['brands' => $brands, 'total' => $total]], 200);
    }

    public function graphTransactionByStatus(Request $request)
    {
        $periods = explode(' - ', $request->period);
        $transactions = $this->financialReportService->graphTransactionByStatusOnPeriod(
            $periods[0],
            $periods[1],
            'd/m/Y'
        );

        $labels = ['Paga', 'Pendente', 'A Receber', 'Chargeback', 'Estornada'];
        return response()->json([
            'labels' => $labels,
            'data' => $transactions
        ], 200);
    }

    /**
     * Return data for Credit Card Status Graph
     * @param Request $request
     * @return JsonResponse
     * @throws BindingResolutionException
     */
    public function graphCreditCardStatusTransactions(Request $request)
    {
        $periods = explode(' - ', $request->period);
        $transactions = $this->financialReportService->graphCreditCardStatusTransactionsOnPeriod(
            $periods[0],
            $periods[1],
            'd/m/Y',
            Auth::user()->platform_id
        );

        $labels = $values = [];

        foreach ($transactions as $transaction) {
            array_push($labels, $transaction->name);
            array_push($values, $transaction->value);
        }

        return $this->customJsonResponse('', 200, ['labels' => $labels, 'values' => $values, 'transactions' => $transactions->toArray()]);
    }

    public function graphTransactionsByInstallments(Request $request)
    {
        $periods = explode(' - ', $request->period);
        $transactions = $this->financialReportService->graphTransactionsByInstallmentsOnPeriod(
            $periods[0],
            $periods[1],
            'd/m/Y'
        );

        $dataBar = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0, 12 => 0];
        $dataLine = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0, 12 => 0];
        foreach ($transactions as $item) {
            $dataBar[$item->installments] = $item->count;
            $dataLine[$item->installments] = $item->total;
        }

        return response()->json([
            'labels' => array_keys($dataBar),
            'dataBar' => array_values($dataBar),
            'dataLine' => array_values($dataLine)
        ], 200);
    }

    public function graphTransactionsByPeriod(Request $request)
    {
        $periods = explode(' - ', $request->period);
        $days = $this->getDaysByPeriod($periods[0], $periods[1], 'd/m/Y');

        $transactionsPaid = $this->financialReportService->transactionsDateOnPeriod(
            $periods[0],
            $periods[1],
            'd/m/Y',
            'paid'
        );

        $transactionsPending = $this->financialReportService->transactionsDateOnPeriod(
            $periods[0],
            $periods[1],
            'd/m/Y',
            'pending'
        );

        $transactionsRefunded = $this->financialReportService->transactionsDateOnPeriod(
            $periods[0],
            $periods[1],
            'd/m/Y',
            'refunded'
        );

        $transactionsChargeback = $this->financialReportService->transactionsDateOnPeriod(
            $periods[0],
            $periods[1],
            'd/m/Y',
            'chargeback'
        );

        $dataBar = $dataPaid = $dataPending = $dataRefunded = $dataChargeback = [];
        foreach ($days as $day) {
            $paid = $transactionsPaid->firstWhere('date', $day)->total ?? 0.0;
            $pending = $transactionsPending->firstWhere('date', $day)->total ?? 0.0;
            $refunded = $transactionsRefunded->firstWhere('date', $day)->total ?? 0.0;
            $chargeback = $transactionsChargeback->firstWhere('date', $day)->total ?? 0.0;

            $dataBar[] = $paid + $pending + $refunded + $chargeback;
            $dataPaid[] = $paid;
            $dataPending[] = $pending;
            $dataRefunded[] = $refunded;
            $dataChargeback[] = $chargeback;
        }

        return response()->json([
            'labels' => $days,
            'dataBar' => $dataBar,
            'dataPaid' => $dataPaid,
            'dataPending' => $dataPending,
            'dataRefunded' => $dataRefunded,
            'dataChargeback' => $dataChargeback
        ]);
    }

    public function getTotalAntecipationFees(Request $request)
    {
        $total = $this->financialReportService
            ->totalAntecipationFees();

        return response()->json(['data' => ['total' => $total]], 200);
    }

    private function getDaysByPeriod($start, $end, $format = 'Y-m-d')
    {
        $days = [];
        if ($format !== 'Y-m-d') {
            $start = DateTime::createFromFormat($format, $start)
                ->format('Y-m-d');

            $end = DateTime::createFromFormat($format, $end)
                ->modify('+1 day')
                ->format('Y-m-d');
        }

        $datePeriod = new DatePeriod(
            new DateTime($start),
            new DateInterval('P1D'),
            new DateTime($end)
        );

        foreach ($datePeriod as $key => $value) {
            $days[] = $value->format('d/m/Y');
        }

        return $days;
    }
}
