<?php

namespace App\Http\Controllers\Getnet;

use App\Plan;
use Illuminate\Support\Facades\Auth;
use stdClass;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use App\Services\Getnet\PlanService;

class PlanController extends Controller
{

    private $planService;
    private $user;

    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            $this->planService = new PlanService($user->platform_id);
            return $next($request);
        });
    }

    public function index()
    {
        $plans = $this->planService->index();

        $recurrence = Plan::allRecurrences();
        $freedays_type = Plan::allFreeDaysType();
        $currency = Plan::allCurrencys();

        return view('getnet.plans.index', compact('plans', 'recurrence', 'freedays_type', 'currency'));
    }

    public function store(Request $request)
    {
        // é usado no PlanController\store;
    }

    public function getPlan($planId)
    {
        $data = $this->planService->getPlan($planId);
        $plan = $data;
        $plan->amount = number_format(substr($data->amount, 0, -2) . '.' . substr($data->amount, -2), 2, ',', '.');

        return view('getnet.plans.edit', compact('plan'));
    }

    public function updatePlan(Request $request, $planId)
    {
        $plan = $this->planService->updatePlan($planId, $request->name, $request->description);

        if ($plan['status'] === 'error') {
            return back()->withErrors(['message' => $plan['data']->message]);
        }

        return redirect('/getnet/plans')->with(['message' => 'Plano "' . $request->name . '" atualizado com sucesso!']);
    }

    public function updateStatusPlan($planId, $planStatus)
    {
        $status = ($planStatus === 'active') ? 'inactive' : 'active';
        $response = $this->planService->updateStatusPlan($planId, $status);

        if ($response['status'] === 'error') {
            return back()->withErrors(['message' => $response['data']['response']]);
        }

        return redirect('/getnet/plans')->with(['message' => 'Status atualizado com sucesso!']);
    }

    public function linksPlans()
    {
        $platform_id = Auth::user()->platform_id;

        $plans = Plan::where('plans.platform_id', '=', $platform_id)
            ->leftJoin('integration_types', 'plans.id', '=', 'integration_types.integratable_id')
            ->leftJoin('integrations', 'integration_types.integration_id', '=', 'integrations.id')
            ->where('integration_types.integratable_type', '=', 'App\Plan')
            ->where('plans.status', '=', '1')
            ->select('plans.id', 'plans.name', 'plans.price', 'plans.platform_id')
            ->get();

        $links = [];

        if ($plans->count() > 0) {
            foreach ($plans as $plan) {
                $links[] = [
                    'name' => $plan->name . ' - (' . number_format($plan->price, 2, ',', '.') . ')',
                    'link' => '/getnet/' . $platform_id . '/' . base64_encode($plan->id)
                ];
            }
        }

        return view('getnet.plans.links', compact('links'));
    }


}
