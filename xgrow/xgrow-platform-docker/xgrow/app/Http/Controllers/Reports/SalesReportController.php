<?php

namespace App\Http\Controllers\Reports;

use App\Client;
use App\Http\Controllers\BankDataController;
use App\Http\Controllers\Controller;
use App\Http\Requests\FinancialExportReportsRequest;
use App\Payment;
use App\PaymentPlanSplit;
use App\Plan;
use App\Platform;
use App\Repositories\Payments\PaymentRepository;
use App\Services\Contracts\SaleReportServiceInterface;
use App\Services\Objects\PeriodFilter;
use App\Services\Objects\SaleReportFilter;
use App\Services\Producer\ProducerService;
use App\Services\Reports\FinancialSaleReportService;
use App\Services\SimpleXLSXGenService;
use App\Transaction;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class SalesReportController extends Controller
{
    private $saleReportService;
    private ProducerService $producerService;

    public function __construct(SaleReportServiceInterface $saleReportService, ProducerService $producerService)
    {
        $this->saleReportService = $saleReportService;
        $this->producerService = $producerService;
    }

    public function index()
    {
        return view('reports.financial.sales');
    }

    public function subscription()
    {
        return view('reports.financial.subscriptions');
    }

    public function exportSale(Request $request)
    {

        /*
        $data = [
            ['Integer', 123],
            ['Float', 12.35],
            ['Procent', '12%'],
            ['Datetime', '2020-05-20 02:38:00'],
            ['Date','2020-05-20'],
            ['Time','02:38:00'],
            ['String', 'Long UTF-8 String in autoresized column'],
            ['Disable Type Detection', "\0".'2020-10-04 16:02:00']
        ];

        https://www.phpclasses.org/package/11656-PHP-Export-data-in-Excel-XLSX-format.html
*/


    }

    public function export(Request $request)
    {

        $payments = $this->getDataReport($request);

        $payments = $payments->get();

        $data[0] = ['Plataforma', 'Código do produto', 'Produto', 'Cliente', 'Documento', 'Valor do produto', 'Taxa', 'Valor líquido', 'Data compra', 'Status', 'Tipo de Pagamento', 'Forma de pagamento', 'Parcelas', 'Situação', 'Email', 'Telefone', 'Rua', 'Número', 'Complemento', 'Bairro', 'Cep', 'Cidade', 'Estado', 'País', 'Código da compra'];

        $total_search = 0;

        foreach ($payments as $payment) {
            //$customer_value =  number_format($payment->customer_value, 2, ',', '.');

            $item = [
                $payment->platform_name,
                $payment->plan_id,
                $payment->plan_name,
                $payment->name,
                $payment->document_number,
                $payment->valor_produto,
                $payment->service_value,
                $payment->customer_value,
                $payment->payment_date,
                $payment->status,
                $payment->type_payment,
                $payment->form_payment,
                $payment->installments,
                $payment->sub_status,
                $payment->email,
                $payment->cel_phone,
                $payment->address_street,
                $payment->address_number,
                $payment->address_comp,
                $payment->address_district,
                $payment->address_zipcode,
                $payment->address_city,
                $payment->address_state,
                $payment->address_country,
                $payment->order_code,
            ];
            array_push($data, $item);
        }

        /*
        $totalization = array_fill(0, sizeof($labels) - 3, '');
        $total_search = 'R$ ' . $total_search;
        $total_sale = 'R$ ' . $this->getTotalSale();
        $totalization = array_merge($totalization,['Total a receber', $total_search, "($total_sale no total)"]);
        array_push($data, $totalization);
        $data = array_merge($data);
        */

        return SimpleXLSXGenService::fromArray($data)->downloadAs('relatorio_vendas.xlsx');

    }

    public function search(Request $request)
    {

        $payments = $this->getDataReport($request);

        return Datatables::of($payments)->make(true);
    }

    private function convertDate($d)
    {
        $date = str_replace("/", "-", $d);
        return date("Y-m-d", strtotime($date));
    }

    private function getDataReport($request)
    {
        $payments = DB::table('payments')
            ->select([DB::raw('
                platforms.id as platform_id,
                platforms.name as platform_name,
                plans.id as plan_id,
                plans.name as plan_name,
                plans.freedays,
                plans.freedays_type,
                plans.recurrence,
                subscribers.id as subscribers_id,
                subscribers.name as subscribers_name,
                subscribers.email,
                subscribers.cel_phone,
                subscribers.address_street,
                subscribers.address_number,
                subscribers.address_comp,
                subscribers.address_district,
                subscribers.address_zipcode,
                subscribers.address_city,
                subscribers.address_state,
                subscribers.address_country,
                subscribers.status as subscribers_status,
                subscribers.document_type,
                subscribers.document_number,
                payments.customer_value,
                payments.payment_date,
                payments.status as payments_status,
                payments.type,
                payments.type_payment,
                payments.charge_code,
                payments.installments,
                payments.boleto_url,
                payments.updated_at as payments_updated_at
            ')])
            ->groupBy('payments.id')
            ->leftJoin('subscribers', 'subscribers.id', '=', 'payments.subscriber_id')
            ->leftJoin('payment_plan', 'payment_plan.payment_id', '=', 'payments.id')
            ->leftJoin('plans', 'plans.id', '=', 'payment_plan.plan_id')
            ->leftJoin('platforms', 'subscribers.platform_id', '=', 'platforms.id')
            ->leftJoin('clients', 'platforms.customer_id', '=', 'clients.id')
            ->where('payments.type', $request->input('paymentType', 'P'))
            ->when($request->name_email, function ($query) use ($request) {
                $query->Where('subscribers.email', 'like', '%' . $request->name_email . '%')
                    ->orWhere('subscribers.name', 'like', '%' . $request->name_email . '%');
            })
            ->when($request->plans, function ($query) use ($request) {
                $query->WhereIn('plans.id', $request->plans);
            })
            ->when($request->status_payment, function ($query) use ($request) {
                $query->WhereIn('payments.status', $request->status_payment);
            })
            ->when($request->type_payment, function ($query) use ($request) {
                $query->where('payments.type_payment', $request->type_payment);
            });

        if ($request->has(['datarange'])) {
            list($inicial, $final) = explode("-", $request->datarange);
            $dataIni = $this->convertDate($inicial);
            $dataFin = $this->convertDate($final);
            if ($dataIni != '') {
                $payments = $payments->where('payments.payment_date', '>=', $dataIni);
            }

            if ($dataFin != '') {
                $payments = $payments->where('payments.payment_date', '<=', $dataFin);
            }
        }

        $payments = $payments->where('platforms.id', '=', Auth::user()->platform_id);

        $payments = $payments->orderBy('payments.payment_date', 'desc');

        return $payments;
    }

    public function searchTransactionData(Request $request): JsonResponse
    {
        try {
            $productsId = $this->rectifyProducts($request->input('productsId'));
            $periodPaymentFilter = $this->convertStringToPeriodFilter($request->input('period'));
            $paymentMethodInput = collect($request->input('paymentMethod'));
            $onlyWithCoupon = $request->input('onlyWithCoupon') === 'true';

            $filters = (new SaleReportFilter())
                ->setSearch($request->input('searchTerm'))
                ->setProducts($productsId)
                ->setPaymentStatus($request->input('statusPayment'))
                ->setPaymentPeriod($periodPaymentFilter)
                ->setPaymentType($request->input('paymentType'))
                ->setOnlyPaymentWithCoupon($onlyWithCoupon);

            if ($paymentMethodInput->contains('multiple_means')) {
                $filters->setOnlyPaymentWithMultipleMeans(true);
                $paymentMethodInput = $paymentMethodInput->push('credit_card');
            }

            $paymentMethods = $paymentMethodInput->reject(function ($value, $key) {
                return $value == 'multiple_means';
            });

            $filters->setPaymentMethod($paymentMethods->toArray());

            $payments = $this->saleReportService->getTransactionSaleReport(
                Auth::user()->platform_id,
                $filters
            );

            return datatables()->of($payments)->toJson();
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()], 400);
        }
    }

    /**
     * @deprecated Replaced by searchTransactionData() on v0.23
     */
    public function searchSingleData(Request $request)
    {
        try {
            $periodFilter = $this->convertStringToPeriodFilter($request->input('period'));

            $filters = (new SaleReportFilter())
                ->setSearch($request->input('searchTerm'))
                ->setProducts($request->input('productsId'))
                ->setPaymentMethod($request->input('typePayment'))
                ->setPaymentStatus($request->input('statusPayment'))
                ->setPaymentPeriod($periodFilter);

            $payments = $this->saleReportService->getSingleSaleReport(
                Auth::user()->platform_id,
                $filters
            );

            return datatables()->of($payments)->toJson();
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()], 400);
        }
    }

    /**
     * @deprecated Replaced by transactionSaleMetrics() on v0.23
     *
     * @param  \Illuminate\Http\Request  $request
     * @return JsonResponse
     */
    public function singleSaleMetrics(Request $request)
    {
        $platformId = Auth::user()->platform_id;
        $periodFilter = $this->convertStringToPeriodFilter($request->input('period'));

        $filters = (new SaleReportFilter())
            ->setSearch($request->input('searchTerm'))
            ->setPlans($request->input('plansId'))
            ->setPaymentMethod($request->input('typePayment'))
            ->setPaymentStatus($request->input('statusPayment'))
            ->setPaymentPeriod($periodFilter);

        $totalPaid = $this->saleReportService->getTotalByAndTypeAndStatus(
            [Payment::TYPE_SALE],
            Payment::STATUS_PAID,
            null,
            null,
            $platformId,
            $filters
        );

        $totalPending = $this->saleReportService->getTotalByAndTypeAndStatus(
            [Payment::TYPE_SALE],
            Payment::STATUS_PENDING,
            null,
            null,
            $platformId,
            $filters
        );

        $totalCancelled = $this->saleReportService->getTotalByAndTypeAndStatus(
            [Payment::TYPE_SALE],
            Payment::STATUS_CANCELED,
            null,
            null,
            $platformId,
            $filters
        );

        $totalChargeback = $this->saleReportService->getTotalByAndTypeAndStatus(
            [Payment::TYPE_SALE],
            Payment::STATUS_CHARGEBACK,
            null,
            null,
            $platformId,
            $filters
        );

        return response()->json(compact(
            'totalPaid',
            'totalPending',
            'totalCancelled',
            'totalChargeback'
        ));
    }

    public function transactionSaleMetrics(Request $request)
    {
        $productsId = $this->rectifyProducts($request->input('productsId'));
        $platformId = Auth::user()->platform_id;
        $periodFilter = $this->convertStringToPeriodFilter($request->input('period'));

        $paymentMethodInput = collect($request->input('paymentMethod'));
        $onlyWithCoupon = $request->input('onlyWithCoupon') === 'true';

        $filters = (new SaleReportFilter())
            ->setSearch($request->input('searchTerm'))
            ->setProducts($productsId)
            ->setPaymentMethod($request->input('paymentMethod'))
            ->setPaymentStatus($request->input('statusPayment'))
            ->setPaymentPeriod($periodFilter)
            ->setPaymentType($request->input('paymentType'))
            ->setOnlyPaymentWithCoupon($onlyWithCoupon);

        if ($paymentMethodInput->contains('multiple_means')) {
            $filters->setOnlyPaymentWithMultipleMeans(true);
            $paymentMethodInput = $paymentMethodInput->push('credit_card');
        }

        $paymentMethods = $paymentMethodInput->reject(function ($value, $key) {
            return $value == 'multiple_means';
        });

        $filters->setPaymentMethod($paymentMethods->toArray());

        $totalPaid = $this->saleReportService->getTotalByAndTypeAndStatus(
            [Payment::TYPE_SALE, Payment::TYPE_UNLIMITED, Payment::TYPE_SUBSCRIPTION],
            Payment::STATUS_PAID,
            null,
            null,
            $platformId,
            $filters
        );

        $totalPending = $this->saleReportService->getTotalByAndTypeAndStatus(
            [Payment::TYPE_SALE, Payment::TYPE_UNLIMITED, Payment::TYPE_SUBSCRIPTION],
            Payment::STATUS_PENDING,
            null,
            null,
            $platformId,
            $filters
        );

        $totalCancelled = $this->saleReportService->getTotalByAndTypeAndStatus(
            [Payment::TYPE_SALE, Payment::TYPE_UNLIMITED, Payment::TYPE_SUBSCRIPTION],
            Payment::STATUS_CANCELED,
            null,
            null,
            $platformId,
            $filters
        );

        $totalChargeback = $this->saleReportService->getTotalByAndTypeAndStatus(
            [Payment::TYPE_SALE, Payment::TYPE_UNLIMITED, Payment::TYPE_SUBSCRIPTION],
            Payment::STATUS_CHARGEBACK,
            null,
            null,
            $platformId,
            $filters
        );

        //$commission = new PaymentRepository;
        // FIXME Remove commission calc
        //$commission = array_sum($commission->reportTransactionSale($platformId, $filters)->get()->pluck('commission')->toArray());
        $commission = 0;

        return response()->json(compact(
            'totalPaid',
            'totalPending',
            'totalCancelled',
            'totalChargeback',
            'commission'
        ));
    }

    public function searchSubscriptionData(Request $request)
    {
        $productsId = $this->rectifyProducts($request->input('productsId'));
        try {
            $periodAccessionFilter = $this->convertStringToPeriodFilter($request->input('periodAccession'));
            $periodCancelFilter = $this->convertStringToPeriodFilter($request->input('periodCancel'));
            $periodLastPaymentFilter = $this->convertStringToPeriodFilter($request->input('periodLastPayment'));

            $filters = (new SaleReportFilter())
                ->setSearch($request->input('searchTerm'))
                ->setProducts($productsId)
                ->setPaymentMethod($request->input('typePayment'))
                ->setSubscriptionStatus($request->input('statusSubscription'))
                ->setAccessionPeriod($periodAccessionFilter)
                ->setCancelPeriod($periodCancelFilter)
                ->setLastPaymentPeriod($periodLastPaymentFilter);

            $payments = $this->saleReportService->getSubscriberSaleReport(
                Auth::user()->platform_id,
                $filters
            );
            return datatables()->eloquent($payments)->toJson();
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()], 400);
        }
    }

    public function subscriptionMetrics(Request $request)
    {
        $productsId = $this->rectifyProducts($request->input('productsId'));
        $platformId = Auth::user()->platform_id;
        $periodAccessionFilter = $this->convertStringToPeriodFilter($request->input('periodAccession'));
        $periodCancelFilter = $this->convertStringToPeriodFilter($request->input('periodCancel'));
        $periodLastPaymentFilter = $this->convertStringToPeriodFilter($request->input('periodLastPayment'));

        $filters = (new SaleReportFilter())
            ->setSearch($request->input('searchTerm'))
            ->setProducts($productsId)
            ->setPaymentMethod($request->input('typePayment'))
            ->setSubscriptionStatus($request->input('statusSubscription'))
            ->setAccessionPeriod($periodAccessionFilter)
            ->setCancelPeriod($periodCancelFilter)
            ->setLastPaymentPeriod($periodLastPaymentFilter);

        $totalActive = $this->saleReportService->getRecurrenceCountByPaymentStatus(
            Payment::STATUS_PAID,
            ['subscriptions.status' => 'active'],
            $platformId,
            $filters
        );

        $totalCancelled = $this->saleReportService->getRecurrenceCountByPaymentStatus(
            Payment::STATUS_PAID,
            ['subscriptions.status' => 'canceled'],
            $platformId,
            $filters
        );

        $totalChargeback = $this->saleReportService->getRecurrenceCountByPaymentStatus(
            Payment::STATUS_CHARGEBACK,
            null,
            $platformId,
            $filters
        );

        $mrr = $this->saleReportService->getMrr($platformId, $filters);

        $churn = 0;
        if ($totalActive > 0 && $totalCancelled > 0) {
            $churn = number_format(($totalCancelled / $totalActive) * 100, 2);
        }

        return response()->json(compact(
            'totalActive',
            'totalCancelled',
            'churn',
            'mrr',
            'totalChargeback'
        ));
    }

    public function subscriptionPayments(Request $request, $subscriber_id, $plan_id, $order_number)
    {
        $orderNumber = ($order_number == 'null') ? null : $order_number;

        $payments = Payment::select(
            'payments.id',
            'payments.order_number',
            'payments.order_code',
            'payments.payment_date',
            'products.name as product_name',
            'plans.name as plan_name',
            'payments.status',
            'payments.type_payment',
            'payments.payment_source',
            'payments.price',
            'payments.tax_value',
            'payments.customer_value',
            'payments.created_at',
            'payments.updated_at'
        )
            ->platform()
            ->leftJoin('payment_plan', 'payment_plan.payment_id', '=', 'payments.id')
            ->leftJoin('plans', 'plans.id', '=', 'payment_plan.plan_id')
            ->leftJoin('products', 'products.id', '=', 'plans.product_id')
            ->where('plans.id', $plan_id)
            ->where('subscriber_id', $subscriber_id)
            ->where('order_number', $orderNumber)
            ->get();

        return $payments;
    }

    public function subscriptionTransactions(Request $request, $order_number)
    {
        return Transaction::select(
            'transactions.id',
            'transactions.status',
            'transactions.type',
            'transactions.transaction_code',
            'transactions.transaction_message',
            'transactions.total',
            'transactions.payment_id',
            'transactions.created_at'
        )
            ->leftJoin('payments', 'payments.id', '=', 'transactions.payment_id')
            ->platform()
            ->where('payments.order_number', '=', $order_number)
            ->orderBy('transactions.created_at', 'DESC')
            ->get();
    }

    public function searchNoLimitData(Request $request)
    {
        try {
            $productsId = $this->rectifyProducts($request->input('productsId'));
            $periodAccessionFilter = $this->convertStringToPeriodFilter($request->input('periodAccession'));
            $periodCancelFilter = $this->convertStringToPeriodFilter($request->input('periodCancel'));
            $periodLastPaymentFilter = $this->convertStringToPeriodFilter($request->input('periodLastPayment'));

            $filters = (new SaleReportFilter())
                ->setSearch($request->input('searchTerm'))
                ->setProducts($productsId)
                ->setPaymentMethod($request->input('typePayment'))
                ->setSubscriptionStatus($request->input('statusSubscription'))
                ->setAccessionPeriod($periodAccessionFilter)
                ->setCancelPeriod($periodCancelFilter)
                ->setLastPaymentPeriod($periodLastPaymentFilter);

            $payments = $this->saleReportService->getNoLimitSaleReport(
                Auth::user()->platform_id,
                $filters
            );
            return datatables()->eloquent($payments)->toJson();
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()], 400);
        }
    }

    public function noLimitMetrics(Request $request)
    {
        $platformId = Auth::user()->platform_id;
        $periodAccessionFilter = $this->convertStringToPeriodFilter($request->input('periodAccession'));
        $periodCancelFilter = $this->convertStringToPeriodFilter($request->input('periodCancel'));
        $periodLastPaymentFilter = $this->convertStringToPeriodFilter($request->input('periodLastPayment'));

        $filters = (new SaleReportFilter())
            ->setSearch($request->input('searchTerm'))
            ->setProducts($request->input('productsId'))
            ->setPaymentMethod($request->input('typePayment'))
            ->setSubscriptionStatus($request->input('statusSubscription'))
            ->setAccessionPeriod($periodAccessionFilter)
            ->setCancelPeriod($periodCancelFilter)
            ->setLastPaymentPeriod($periodLastPaymentFilter);

        $totalToReceive = $this->saleReportService->getTotalByAndTypeAndStatus(
            [Payment::TYPE_UNLIMITED],
            Payment::STATUS_PAID,
            ['payments.payment_date' => ['op' => '<', 'value' => Carbon::now()]],
            null,
            $platformId,
            $filters
        );

        $totalPending = $this->saleReportService->getTotalByAndTypeAndStatus(
            [Payment::TYPE_UNLIMITED],
            Payment::STATUS_PENDING,
            null,
            null,
            $platformId,
            $filters
        );

        $totalLate = $this->saleReportService->getTotalByAndTypeAndStatus(
            [Payment::TYPE_UNLIMITED],
            Payment::STATUS_FAILED,
            null,
            null,
            $platformId,
            $filters
        );

        $totalCancelled = $this->saleReportService->getTotalByAndTypeAndStatus(
            [Payment::TYPE_UNLIMITED],
            Payment::STATUS_CANCELED,
            null,
            null,
            $platformId,
            $filters
        );

        $totalChargeback = $this->saleReportService->getTotalByAndTypeAndStatus(
            [Payment::TYPE_UNLIMITED],
            Payment::STATUS_CHARGEBACK,
            null,
            null,
            $platformId,
            $filters
        );

        return response()->json(compact(
            'totalToReceive',
            'totalPending',
            'totalLate',
            'totalCancelled',
            'totalChargeback'
        ));
    }

    public function noLimitPayments(Request $request, $subscriber_id, $plan_id, $order_number)
    {
        $platform = Platform::with(['client:id,percent_split'])->findOrFail(Auth::user()->platform_id);

        $orderNumber = ($order_number == 'null') ? null : $order_number;

        $percentSplit = $platform->client->percent_split ?? 100;
        $clientTax = (100 - $percentSplit) / 100;

        $payments = Payment::select(
            'payments.id',
            'payments.order_number',
            'payments.order_code',
            'payments.payment_date',
            'products.id as product_id',
            'products.name',
            'payments.status',
            'payments.type_payment',
            'payments.payment_source',
            'payments.price',
            'payments.tax_value',
            'payments.installments as payment_installments',
            'payments.tax_value as payment_tax_value',
            'payments.customer_value',
            'payment_plan.tax_value as payment_plan_tax_value',
            'payment_plan.plan_value as payment_plan_plan_value',
            'payment_plan.plan_price as payment_plan_plan_price',
            'payment_plan.coupon_id as payment_plan_coupon_id',
            'payment_plan.coupon_code as payment_plan_coupon_code',
            'payment_plan.coupon_value as payment_plan_coupon_value',
            'payment_plan.type as payment_plan_type',
            'payment_plan.customer_value as payment_plan_customer_value',
            DB::raw('(
                SELECT GROUP_CONCAT(DISTINCT p.order_bump_plan_id)
                FROM payment_plan pp
                JOIN plans p ON p.id = pp.plan_id
                WHERE pp.payment_id = payments.id
                GROUP BY pp.payment_id
                ORDER BY pp.created_at DESC
            ) AS payment_order_bump'),
            'plans.price as plan_price',
            'coupons.value as coupon_value',
            'coupons.code as coupon_code',
            DB::raw("{$clientTax} as client_tax")
        )
            ->platform()
            ->leftJoin('payment_plan', 'payment_plan.payment_id', '=', 'payments.id')
            ->leftJoin('plans', 'plans.id', '=', 'payment_plan.plan_id')
            ->leftJoin('products', 'products.id', '=', 'plans.product_id')
            ->leftJoin('coupons', 'coupons.id', '=', 'payments.coupon_id')
            ->where('plans.id', $plan_id)
            ->where('subscriber_id', $subscriber_id)
            ->where('order_number', $orderNumber)
            ->get();

        return $payments;
    }

    public function getCommisions($paymentId)
    {
        $paymentsCommision = PaymentPlanSplit::getCommisions($paymentId);

        return $paymentsCommision;
    }


    /**
     * @param FinancialExportReportsRequest $request
     * @return void
     */
    public function financialExportReports(FinancialExportReportsRequest $request)
    {
        $reportName = $request->input('reportName') ?? 'transactions';

        $saleReportService = new FinancialSaleReportService;

        $saleReportService->financialExportReport($reportName, $request->all());
    }

    public function exportReports(Request $request)
    {
        $productsFilter = $this->rectifyProducts($request->input('exportProductsFilter'));
        $typePaymentFilter = $request->input('exportPaymentTypeFilter') ?? null;
        $methodPaymentFilter = $request->input('exportMethodFilter') ?? null;
        $paymentMethodInput = collect($request->input('exportMethodFilter') ?? []);
        $onlyWithCoupon = $request->input('onlyWithCoupon');
        $statusPaymentFilter = $request->input('exportStatusFilter') ?? null;
        $periodPaymentFilter = $this->convertStringToPeriodFilter($request->input('exportPeriodFilter'));
        $statusSubscriptionFilter = $request->input('exportStatusSubscriptionFilter') ?? null;
        $periodAccessionFilter = $this->convertStringToPeriodFilter($request->input('exportPeriodAccessionFilter'));
        $periodCancelFilter = $this->convertStringToPeriodFilter($request->input('exportPeriodCancelFilter'));
        $periodLastPaymentFilter = $this->convertStringToPeriodFilter($request->input('exportPeriodLastPaymentFilter'));
        $searchTermFilter = $request->input('searchTermFilter') ?? null;
        $typeFile = $request->input('typeFile') ?? 'xlsx';
        $reportName = $request->input('reportName') ?? 'single';

        $filters = (new SaleReportFilter())
            ->setSearch($searchTermFilter)
            ->setProducts($productsFilter)
            ->setPaymentType($typePaymentFilter)
            ->setPaymentMethod($methodPaymentFilter)
            ->setPaymentStatus($statusPaymentFilter)
            ->setPaymentPeriod($periodPaymentFilter)
            ->setSubscriptionStatus($statusSubscriptionFilter)
            ->setAccessionPeriod($periodAccessionFilter)
            ->setCancelPeriod($periodCancelFilter)
            ->setLastPaymentPeriod($periodLastPaymentFilter)
            ->setOnlyPaymentWithCoupon($onlyWithCoupon);

        if ($paymentMethodInput->contains('multiple_means')) {
            $filters->setOnlyPaymentWithMultipleMeans(true);
            $paymentMethodInput->push('credit_card');
        }

        $paymentMethods = $paymentMethodInput->reject(function ($value, $key) {
            return $value == 'multiple_means';
        });

        $filters->setPaymentMethod($paymentMethods->toArray());

        $this->saleReportService->exportReport(
            $reportName,
            $typeFile,
            Auth::user(),
            $filters
        );
    }

    public function convertDateSearch(string $date)
    {
        $date = explode('/', $date);
        return "$date[2]-$date[1]-$date[0]";
    }

    /**
     * Convert period string to PeriodFilter object
     *
     * @param  string|null  $inputPeriod  format 'dd/mm/YYYY-dd/mm/YYYY'
     * @return \App\Services\Objects\PeriodFilter|null PeriodFilter if parsed successfully, null otherwise
     */
    private function convertStringToPeriodFilter(?string $inputPeriod): ?PeriodFilter
    {
        if (is_null($inputPeriod)) {
            return null;
        }

        if (!preg_match('~\d{2}/\d{2}/\d{4}-\d{2}/\d{2}/\d{4}$~', $inputPeriod)) {
            return null; // unexpected format
        }

        $fields = $inputPeriod ? explode('-', $inputPeriod) : ['', ''];
        $begin = $this->convertDateSearch($fields[0]);
        $end = $this->convertDateSearch($fields[1]);

        if (!validateDate($begin, 'Y-m-d') || !validateDate($end, 'Y-m-d')) {
            return null;
        }

        try {
            return new PeriodFilter($begin, $end);
        } catch (Exception $e) {
            Log::error('Erro ao converter data no filtro. '.$e->getMessage());
        }

        return null;
    }

    /**
     * Rectify products filter based on user's role (owner/producer)
     *
     * @param  array|null  $productsId
     * @return array|null
     */
    private function rectifyProducts(?array $productsId)
    {
        if (!Auth::isProducer()) {
            // owner can view all products
            return $productsId;
        }

        $producerProducts = $this->producerService
            ->listProductsFromProducer(Auth::user()->id)
            ->pluck('id')->toArray();

        if ($productsId) {
            // only selected products from producer
            return array_intersect($productsId, $producerProducts);
        }

        // all products from producer
        return $producerProducts;
    }

}

