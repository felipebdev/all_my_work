<?php

namespace App\Http\Controllers;

use App\Client;
use App\Dashboard;
use App\Http\Traits\CustomResponseTrait;
use App\Payment;
use App\Platform;
use App\Product;
use App\Repositories\Dashboard\ProductSaleRepository;
use App\Services\Auth\ClientStatus;
use App\Services\DashboardService;
use App\Services\LAService;
use App\Subscriber;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{

    use CustomResponseTrait;

    private $dashboardModel;
    private $subscriber;

    public function __construct(Dashboard $dashboard, Subscriber $subscriber)
    {
        $this->dashboardModel = $dashboard;
        $this->subscriber = $subscriber;
    }

    public function index(DashboardService $dashboardService, Request $request)
    {
        $platform_id = Auth::user()->platform_id;
        //$infoOnline = $dashboardService->getOnlineUsers($platform_id);

        $initialDate = Carbon::now()->subMonths(1)->format('d/m/Y');
        $finalDate = Carbon::now()->format('d/m/Y');;
        $period = $initialDate . ' - ' . $finalDate;
        $search = ['period' => $period];

        $status = ClientStatus::withPlatform($platform_id, Auth::user()->email);

        return view('dashboard.index', [
            //'infoOnline' => $infoOnline,
            'search' => $search,
            'clientApproved' => $status->clientApproved,
            'recipientStatusMessage' => $status->recipientStatusMessage,
            'verifyDocument' => $status->mustVerify
        ]);
    }

    public function getSubscribersBarSummary(Request $request)
    {
        try {
            [$start, $end] = $this->convertPeriod($request->period);


            $activeSubscribers = Subscriber::select('id')
                ->where('platform_id', Auth::user()->platform_id)
                ->where('created_at', '>=', $start)
                ->where('created_at', '<=', $end)
                ->where('status', 'active')
                ->count();

            $activeSubscribersPercentage = 0;

            if ($activeSubscribers > 0) {
                $oldActiveSubscribers = Subscriber::select('id')
                    ->where('platform_id', Auth::user()->platform_id)
                    ->where('created_at', '<', $start)
                    ->where('status', 'active')
                    ->count();

                $activeSubscribersPercentage = ($oldActiveSubscribers === 0)? 0 : (100 * $activeSubscribers) / $oldActiveSubscribers;
            }

            $newSubscribers = Subscriber::select('id')
                ->where('platform_id', Auth::user()->platform_id)
                ->where('status', '<>', 'lead')
                ->where('created_at', '>=', $start)
                ->where('created_at', '<=', $end)
                ->count();

            $cancelledSubscribers = Subscriber::select('id')
                ->where('platform_id', Auth::user()->platform_id)
                ->where('status', 'canceled')
                ->where('created_at', '>=', $start)
                ->where('created_at', '<=', $end)
                ->count();

            $automaticRegisteredPayingStudents = Subscriber::select('id')
                ->where('platform_id', Auth::user()->platform_id)
                ->where('source_register', 'checkout')
                ->where('status', 'active')
                ->where('created_at', '>=', $start)
                ->where('created_at', '<=', $end)
                ->count();

            return $this->customJsonResponse('Dados carregados sucesso!', 200, [
                'activeSubscribers' => $activeSubscribers,
                'activeSubscribersPercentage' => $activeSubscribersPercentage,
                'newSubscribers' => $newSubscribers,
                'cancelledSubscribers' => $cancelledSubscribers,
                'automaticRegisteredPayingStudents' => $automaticRegisteredPayingStudents
            ]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage() . ' ' . $e->getLine(), 400, []);
        }
    }

    public function subscribersStatus()
    {
        try {
            $sqlSLead = $this->dashboardModel->getSubscribersByStatus(Auth::user()->platform_id, 'lead');
            $sqlSActive = $this->dashboardModel->getSubscribersByStatus(Auth::user()->platform_id, 'active');
            $sqlSCancelled = $this->dashboardModel->getSubscribersByStatus(Auth::user()->platform_id, 'canceled');
            $sqlSTrial = $this->dashboardModel->getSubscribersByStatus(Auth::user()->platform_id, 'trial');
            $sqlSTotal = $this->dashboardModel->getSubscribersByStatus(Auth::user()->platform_id);

            /* Cálculo da previsão de faturamento */
            $platform = Platform::findOrFail(Auth::user()->platform_id);
            $client = Client::findOrFail($platform->customer_id);
            $sumSales = Payment::where('platform_id', '=', Auth::user()->platform_id)
                ->where('status', '=', 'paid')
                ->sum('customer_value');
            $sumPending = Payment::where('platform_id', '=', Auth::user()->platform_id)
                ->where('status', '=', 'pending')
                ->sum('customer_value');
            /* Fim do cálculo da previsão de faturamento */

            $subscribersLead = DB::select($sqlSLead);
            $subscribersActive = DB::select($sqlSActive);
            $subscribersCancelled = DB::select($sqlSCancelled);
            $subscribersTrial = DB::select($sqlSTrial);
            $subscribersTotal = DB::select($sqlSTotal);
            $subscriberOnline = $this->getTotalOnlineSubscribers();

            $labels = $data = [];
            return response()->json([
                'labels' => $labels,
                'data' => $data,
                'qtdLead' => count($subscribersLead),
                'qtdOnline' => $subscriberOnline,
                'qtdActive' => count($subscribersActive),
                'qtdCancelled' => count($subscribersCancelled),
                'qtdTrial' => count($subscribersTrial),
                'qtdTotal' => count($subscribersTotal),
                'billingPrevision' => $sumSales + $sumPending,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);
        }
    }

    public function getSubscribersStatusByPeriod(Request $request)
    {
        [$start, $end] = $this->convertPeriod($request->period);

        try {
            $sql = $this->dashboardModel->getSubscribersGroupByStatus(Auth::user()->platform_id, $start ?? null, $end ?? null);
            $subscribersStatusByPeriod = DB::select($sql);

            $labels = $data = [];
            $subscribersActives = $subscribersCancelled = [];

            foreach ($subscribersStatusByPeriod as $item) {
                //usar dois ifs de status com data
                $labels[] = date('d/m/Y', strtotime($item->date));
                if ($item->status == 'active') {
                    array_push($subscribersActives, ['amount' => $item->amount, 'date' => date('d/m/Y', strtotime($item->date))]);
                };
                if ($item->status == 'canceled') {
                    array_push($subscribersCancelled, ['amount' => $item->amount, 'date' => date('d/m/Y', strtotime($item->date))]);
                };
            }

            $subActives = $subCancelled = $subTotal = [];

            $saAnterior = 0;
            foreach (array_unique($labels) as $label) {
                $boolSActive = false;
                foreach ($subscribersActives as $sa) {
                    if ($label === $sa['date']) {
                        array_push($subActives, $sa['amount'] + $saAnterior);
                        $saAnterior = $sa['amount'] + $saAnterior;
                        $boolSActive = true;
                    }
                }
                if (!$boolSActive) {
                    array_push($subActives, 0 + $saAnterior);
                }
            }

            foreach (array_unique($labels) as $label) {
                $boolSActive = false;
                foreach ($subscribersCancelled as $sc) {
                    if ($label === $sc['date']) {
                        array_push($subCancelled, $sc['amount']);
                        $boolSActive = true;
                    }
                }
                if (!$boolSActive) {
                    array_push($subCancelled, 0);
                }
            }

            for ($i = 0; $i < count(array_unique($labels)); $i++) {
                array_push($subTotal, $subActives[$i] + $subCancelled[$i]);
            }

            return response()->json([
                'labels' => array_values(array_unique($labels)),
                'data' => ['active' => $subActives, 'cancelled' => $subCancelled, 'total' => $subTotal],
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);
        }
    }

    public function lastAccess()
    {
        try {
            $sql = $this->dashboardModel->getLastAccess(Auth::user()->platform_id);
            $getLastAccess = DB::select($sql);

            return response()->json([
                'data' => $getLastAccess,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);
        }
    }

    public function getNewSubscribers()
    {
        try {
            $subscribers = $this->subscriber
                ->select(DB::raw('subscribers.name, subscribers.created_at, files.filename, payments.status'))
                ->leftJoin('files', 'subscribers.thumb_id', '=', 'files.id')
                ->leftJoin('payments', 'subscribers.id', '=', 'payments.subscriber_id')
                ->where(['subscribers.platform_id' => Auth::user()->platform_id])
                ->where(['payments.status' => 'paid'])
                ->orderByDesc('subscribers.created_at')
                ->limit(10)
                ->distinct()
                ->get();
            return response()->json([
                'data' => $subscribers,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);
        }
    }


    public function getNewSubscribersByPeriod(Request $request)
    {
        try {

            [$start, $end] = $this->convertPeriod($request->period);
            $platform_id = Auth::user()->platform_id;

            $sql = $this->dashboardModel->getNewsSubscribers($platform_id, $start, $end, 'paid', 20);
            $subscribers = DB::select($sql);

            return response()->json([
                'data' => $subscribers,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);
        }
    }

    public function getTop10Contents(Request $request)
    {
        try {
            $sql = $this->dashboardModel->mostAccessedContent(Auth::user()->platform_id);
            $getNewSubscribers = DB::select($sql);

            return response()->json([
                'data' => $getNewSubscribers,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);
        }
    }


    public function getCoursesSales(Request $request)
    {
        try {
            $sql = $this->dashboardModel->getCoursesSales(Auth::user()->platform_id);
            $getCoursesSales = DB::select($sql);
            $labels = $data = [];
            foreach ($getCoursesSales as $item) {
                array_push($labels, $item->name);
                array_push($data, $item->amount);
            }
            return response()->json([
                'labels' => $labels,
                'data' => $data,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);
        }
    }

    /**
     * @return int
     */
    private function getTotalOnlineSubscribers()
    {
        try {
            $platform_id = Auth::user()->platform_id;
            $user_id = Auth::user()->id;

            $laService = new LAService($platform_id, $user_id);
            $totalOnlineUsers = $laService->getTotalOnlineUsers()->data->totalOnlineUsers;

            return $totalOnlineUsers;
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);
        }
    }

    public function getOnlineUsers()
    {
        try {
            $platform_id = Auth::user()->platform_id;
            $user_id = Auth::user()->id;

            $laService = new LAService($platform_id, $user_id);
            $res = $laService->getOnlineUsers();

            $onlineUsers = $res->data;

            $userIds = array_column($onlineUsers, '_id');

            $data = $this->subscriber
                ->select(DB::raw('subscribers.name, subscribers.email, files.filename'))
                ->leftJoin('files', 'subscribers.thumb_id', '=', 'files.id')
                ->whereIn('subscribers.id', $userIds)
                ->limit(100)
                ->get();

            return response()->json([
                'data' => $data,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);
        }
    }

    public function convertPeriod($period)
    {
        $periods = explode(' - ', $period);
        $startPeriod = $periods[0];
        $startPeriod = explode('/', $startPeriod);
        $endPeriod = $periods[1];
        $endPeriod = explode('/', $endPeriod);
        return [$startPeriod[2] . '-' . $startPeriod[1] . '-' . $startPeriod[0] . ' 00:00:00', $endPeriod[2] . '-' . $endPeriod[1] . '-' . $endPeriod[0] . ' 23:59:59'];
    }

    public function getPlanSales(Request $request)
    {
        try {
            $period = $request->input('period');
            [$begin, $end] = $this->convertPeriod($period);
            $getPlansSales = $this->dashboardModel->getPlanSales(Auth::user()->platform_id, $begin, $end);
            $labels = $data = [];
            foreach ($getPlansSales as $item) {
                array_push($labels, $item->plan_name);
                array_push($data, ['value' => $item->sales_count, 'name' => $item->plan_name]);
            }
            return response()->json([
                'labels' => $labels,
                'data' => $data,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);
        }
    }

    /**
     * Get products by platform
     * @return JsonResponse
     */
    public function getProductsByPlatform(): JsonResponse
    {
        try {
            $products = Product::select([
                'id',
                'name'
            ])
                ->where('platform_id', Auth::user()->platform_id)
                ->get();

            $products = collect($products)->pluck('name', 'id');

            return $this->customJsonResponse('', 200, ['data' => $products]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400);
        }
    }

    public function getProductSaleByPeriod(Request $request)
    {
        try {

            [$start, $end] = $this->convertPeriod($request->period);

            $data = (new ProductSaleRepository)->getData($start, $end, $request->product_id);

            return $this->customJsonResponse('', 200, ['data' => $data]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400);
        }
    }

    public function userInfo()
    {
        try {
            return $this->customJsonResponse('', 200, [
                'name' => Auth::user()->name, 'email' => Auth::user()->email

            ]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 500);
        }
    }
}
