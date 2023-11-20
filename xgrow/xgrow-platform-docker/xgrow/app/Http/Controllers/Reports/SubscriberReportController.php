<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Plan;
use App\Subscriber;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class SubscriberReportController extends Controller
{
    public function index()
    {
        $subscribers = Subscriber::select('subscribers.id')
            ->where('subscribers.platform_id', Auth::user()->platform_id)
            ->where('subscribers.status', '!=', Subscriber::STATUS_LEAD)
            ->orderBy('subscribers.created_at', 'DESC')
            ->get();

        $plans = Plan::select(['plans.id', 'plans.name'])
            ->where('plans.platform_id', Auth::user()->platform_id)
            ->where('plans.status', '=', true)
            ->get();

        $total = count($subscribers);

        return view('reports.subscriber.index', compact('total', 'plans'));
    }

    public function getSubscribers(Request $request)
    {
        try {
            // Subquery para trazer todos os planos de um subscriber
            // $subscriptionSQL = '
            // (SELECT GROUP_CONCAT(plans.name SEPARATOR ",")
            // FROM subscriptions
            // LEFT JOIN plans ON subscriptions.plan_id = plans.id
            // WHERE subscribers.id = subscriptions.subscriber_id
            // AND subscriptions.payment_pendent IS NULL
            // ) AS plans';

            $subscribers = Subscriber::select(
                'subscribers.id',
                'subscribers.name',
                'subscribers.email',
                'subscribers.main_phone',
                'subscribers.created_at',
                'subscribers.last_acess',
                'subscribers.status',
                'plans.name AS plans'
            )
                ->leftJoin('subscriptions', 'subscriptions.subscriber_id', '=', 'subscribers.id')
                ->leftJoin('plans', 'plans.id', '=', 'subscriptions.plan_id')
                ->where('subscribers.platform_id', Auth::user()->platform_id)
                ->where('subscribers.status', '!=', Subscriber::STATUS_LEAD)
                ->orderBy('subscribers.created_at', 'DESC')
                ->whereNull('subscriptions.payment_pendent')
                ->get();

            return response()->json([
                'data' => $subscribers,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()], 400);
        }
    }
}
