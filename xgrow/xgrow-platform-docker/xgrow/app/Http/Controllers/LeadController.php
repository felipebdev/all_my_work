<?php

namespace App\Http\Controllers;

use App\Http\Traits\CustomResponseTrait;
use App\Services\Contracts\LeadReportServiceInterface;
use App\Services\Objects\LeadReportFilter;
use App\Subscriber;
use App\Transaction;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */

    private $leadReportService;

    use CustomResponseTrait;

    public function __construct(LeadReportServiceInterface $leadReportService)
    {
        $this->leadReportService = $leadReportService;
    }

    public function index()
    {
        return view('leads.index');
    }

    public function leadsData(Request $request)
    {
        $query = Subscriber::where('subscribers.platform_id',  Auth::user()->platform_id)
            ->leftJoin('plans', 'subscribers.plan_id', '=', 'plans.id')
            ->select('subscribers.id', 'subscribers.document_number', 'subscribers.name', 'subscribers.cel_phone', 'subscribers.main_phone', 'subscribers.email', 'subscribers.created_at', 'subscribers.last_acess', 'subscribers.status', 'plans.name AS plan_name', 'plans.id AS plan_id')
            ->where('subscribers.status', '=', Subscriber::STATUS_LEAD)
            ->latest();

        return datatables()->eloquent($query)
            ->filterColumn('created_at', function ($qr, $value) {
                if (empty($value)) return;
                list($start, $end) = explode('/', $value);
                $qr->whereBetween('subscribers.created_at', [$start, $end]);
            })
            ->toJson();
    }

    public function searchLeads(Request $request)
    {
        try {
            $createdPeriodFilter = ($request->input('createdPeriodFilter')) ? explode('-', $request->input('createdPeriodFilter')) : ['', ''];
            $filters = new LeadReportFilter(
                $request->input('searchTermFilter'),
                $request->input('plansFilter'),
                parseBrDate($createdPeriodFilter[0]),
                parseBrDate($createdPeriodFilter[1]),
                $request->input('onlyFailedTransactions')
            );

            $leads = $this->leadReportService->getLeadsReport(
                Auth::user()->platform_id,
                $filters
            );

            return datatables()->of($leads)->toJson();
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()], 400);
        }
    }

    public function exportLeads(Request $request)
    {
        $searchTermFilter = $request->input('searchTermFilter') ?? null;
        $plansFilter = $request->input('plansFilter') ?? null;
        $onlyFailedTransactions = $request->input('onlyFailedTransactions') ?? null;
        $createdPeriodFilter = ($request->input('createdPeriodFilter')) ? explode('-', $request->input('createdPeriodFilter')) : ['', ''];
        $typeFile = $request->input('typeFile') ?? 'xlsx';
        $reportName = $request->input('reportName') ?? 'subscriber-users';

        $filters = new LeadReportFilter(
            $searchTermFilter,
            $plansFilter,
            parseBrDate($createdPeriodFilter[0]),
            parseBrDate($createdPeriodFilter[1]),
            $onlyFailedTransactions
        );

        $this->leadReportService->exportLeadReport(
            $reportName,
            $typeFile,
            Auth::user(),
            $filters
        );
    }

    /**
     * Get payment request for leads and show in details frontend
     * @param Request $request
     * @return JsonResponse|void
     */
    public function getPaymentStatus(Request $request, $uid)
    {
        try {
            $details = Transaction::select([
                'transactions.status',
                'transactions.transaction_message',
                'transactions.created_at'
            ])
                ->leftJoin('payments', 'transactions.order_code', '=', 'payments.order_code')
                ->leftJoin('payment_plan', 'payments.id', '=', 'payment_plan.payment_id')
                ->leftJoin('plans', 'payment_plan.plan_id', '=', 'plans.id')
                ->leftJoin('products', 'plans.product_id', '=', 'products.id')
                ->where('transactions.subscriber_id', $uid)
                ->where('transactions.status', 'failed')
                ->get();

            return $this->customJsonResponse('', 200, ['details' => $details]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }
}
