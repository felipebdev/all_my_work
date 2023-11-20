<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Mundipagg\RecipientController;
use App\Payment;
use App\Services\Checkout\BalanceService;
use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class FinancialMobileController extends Controller
{
    private RecipientController $recipientController;

    private BalanceService $balanceService;

    public function __construct(RecipientController $recipientController, BalanceService $balanceService)
    {
        $this->recipientController = $recipientController;
        $this->balanceService = $balanceService;
    }

    /**
     * @return JsonResponse
     * @throws \MundiAPILib\APIException
     */
    public function balance(): JsonResponse
    {
        try {
            $data = $this->balanceService->getUserClientBalance(Auth::user()->platform_id, Auth::user()->id);

            return response()->json([
                'available_amount' => self::moneyFormat($data->available),
                'waiting_funds_amount' => self::moneyFormat($data->pending),
                'transferred_amount' => self::moneyFormat($data->transferred),
                'total_invoicing' => self::moneyFormat($data->available + $data->transferred),
                'total_anticipation' => self::moneyFormat($data->anticipation),
            ]);
        } catch (ClientException $e) {
            return response()->json([
                'available_amount' => self::moneyFormat(0),
                'waiting_funds_amount' => self::moneyFormat(0),
                'transferred_amount' => self::moneyFormat(0),
                'total_invoicing' => self::moneyFormat(0),
                'total_anticipation' => self::moneyFormat(0),
            ]);
        } catch (ConnectException $e) {
            Log::error('Erro ao conectar na API do checkout', ['message' => json_encode($e->getMessage())]);
            return response()->json(['error' => 'error_receiving_balance'], Response::HTTP_MISDIRECTED_REQUEST);
        }
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     * @throws \MundiAPILib\APIException
     */
    public function withdrawValue(Request $request): JsonResponse
    {
        return response()->json($this->recipientController->sendWithdrawal($request));
    }

    /**
     * @return JsonResponse
     */
    public function listWithdrawals()
    {
        try {
            return response()->json($this->recipientController->listWithdrawals());
        } catch (\Throwable $th) {
            return response()->json(['error' => 'data_not_found'], 404);
        }
    }

    /**
     * @param  Request  $request
     * @return array
     */
    public function allReporsts(Request $request): array
    {
        $period = [];

        if (isset($request->period)) {

            switch ($request->period) {

                case 2:
                    $request->request->add(['start_date' => subtractDate(7)]);
                    break;

                case 3:
                    $request->request->add(['start_date' => subtractDate(15)]);
                    break;

                case 4:
                    $request->request->add(['start_date' => subtractDate(30)]);
                    break;

                case 1:
                default:
                    $request->request->add(['start_date' => date('Y-m-d')]);
                    break;
            }

            $period = [$this->convertDate($request->start_date, '-1'), $this->convertDate(date('Y-m-d'), '+1')];
        } elseif (isset($request->start_date) && isset($request->end_date)) {
            $period = [$this->convertDate($request->start_date, '-1'), $this->convertDate($request->end_date, '+1')];
        }

        $period = sizeof($period) > 0 ? $period : null;

        $amountGraph = Payment::select(
            DB::raw('ROUND(sum(payment_plan.customer_value), 2) as value'),
            'payments.payment_date as date'
        )
            ->join('payment_plan', 'payment_plan.payment_id', 'payments.id')
            ->where('payments.platform_id', Auth::user()->platform_id)
            ->when($period, function ($q, $period) {
                $q->whereBetween('payments.payment_date', $period);
            })
            ->groupBy('payments.payment_date')
            ->get();

        $aggregatedQuery = fn ($searchStatus, $youWantToDo, $value) => $this->aggregatedQuery(
            $this->paymentsReport($period),
            $value,
            $youWantToDo,
            $searchStatus
        );

        return [
            'paid' => round($aggregatedQuery(Payment::STATUS_PAID, 'sum', 'payment_plan_customer_value'), 2),
            'pending' => round($aggregatedQuery(Payment::STATUS_PENDING, 'sum', 'payment_plan_customer_value'), 2),
            'canceled' => round($aggregatedQuery(Payment::STATUS_CANCELED, 'sum', 'payment_plan_customer_value'), 2),
            'failed' => round($aggregatedQuery(Payment::STATUS_FAILED, 'sum', 'payment_plan_customer_value'), 2),
            'chargeback' => round($aggregatedQuery(Payment::STATUS_CHARGEBACK, 'sum', 'payment_plan_customer_value'), 2),
            'expired' => round($aggregatedQuery(Payment::STATUS_EXPIRED, 'sum', 'payment_plan_customer_value'), 2),
            'expired' => round($aggregatedQuery(Payment::STATUS_EXPIRED, 'sum', 'payment_plan_customer_value'), 2),
            'refunded' => round($aggregatedQuery(Payment::STATUS_REFUNDED, 'sum', 'payment_plan_customer_value'), 2),
            'pending_refund' => round($aggregatedQuery(Payment::STATUS_PENDING_REFUND, 'sum', 'payment_plan_customer_value'), 2),
            'payments_on_period' => $this->paymentsReport($period)->paginate(25),
            'label' => $amountGraph
        ];
    }

    /**
     * @param $value
     * @return false|string
     */
    private static function moneyFormat($value)
    {
        $fraction = substr($value, -2);

        $value = substr($value, 0, -2);

        $value = $value . "." . $fraction;

        return floatval($value);
    }


    /**
     * @param  null  $period
     * @return mixed
     */
    public function paymentsReport($period = null)
    {
        $query = Payment::select(
            'products.name as product_name',
            'plans.name as plan_name',
            'payments.status',
            'payments.payment_date',
            DB::raw("
                CASE
                    WHEN payment_plan.customer_value IS null THEN
                         IF(payment_plan.type = 'order_bump', 0, payments.customer_value)
                    ELSE
                       payment_plan.customer_value
                END AS payment_plan_customer_value
            "),
        )
            ->join('payment_plan', 'payment_plan.payment_id', 'payments.id')
            ->join('plans', 'payment_plan.plan_id', 'plans.id')
            ->join('products', 'plans.product_id', 'products.id')
            ->where('payments.platform_id', Auth::user()->platform_id)
            ->when($period, function ($q, $period) {
                $q->whereBetween('payments.payment_date', $period);
            })->orderBy('payments.payment_date', 'DESC');

        return $query;
    }

    /**
     * @param $date
     * @param $modify
     * @return string
     */
    public function convertDate($date, $modify): string
    {
        $dateTime = Carbon::createFromFormat('Y-m-d', $date);

        if ($modify === '-1') {

            $dateTime->startOfDay();
        } else {

            $dateTime->endOfDay();
        }

        return $dateTime->format('Y-m-d H:i:s');
    }

    /**
     * @param $subQuery
     * @param $value
     * @param $youWantToDo
     * @return mixed
     */
    public function aggregatedQuery($subQuery, $value, $youWantToDo, $searchStatus = null)
    {
        $subquerySql = $subQuery->where('payments.status', $searchStatus)->toSql();

        return DB::table(DB::raw("($subquerySql) as subquery"))
            ->mergeBindings($subQuery->getQuery())
            ->$youWantToDo("subquery.$value");
    }
}
