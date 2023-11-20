<?php

namespace App\Services\Mundipagg;

use App\Constants;
use MundiAPILib\Models\UpdatePlanRequest;
use MundiAPILib\Models\CreatePlanItemRequest;
use MundiAPILib\Models\CreatePricingSchemeRequest;
use MundiAPILib\Models\CreatePlanRequest;
use Auth;
use App\Integration;
use App\Plan;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use App\Services\MundipaggService;

class PlanService extends Controller
{
    private $plansController;

    public function __construct($platform_id)
    {
        $mundipaggService = new MundipaggService();
        $client = $mundipaggService->getClient();
        $this->plansController = $client->getPlans();
    }

    public function store($plan)
    {
        $installments = ($plan->installment > 0) ? $plan->installment : 1;

        $request = new CreatePlanRequest();
        $request->name = $plan->name;
        $request->description = $plan->description;
        $request->currency = "BRL";
        $request->interval = ($plan->type_plan === 'P') ? "month" : Plan::getIntervalMundi($plan->recurrence);
        $request->intervalCount = ($plan->charge_until > 0) ? $plan->charge_until : 1;
        $request->billingType = "exact_day";
        $request->billingDays = [(int) date('d', strtotime($plan->created_at))];
        $request->minimumPrice = str_replace('.', '', $plan->price);
        $request->installments = [$installments];
        $request->paymentMethods = ["credit_card"];
        $request->items = [
            new CreatePlanItemRequest(),
            new CreatePlanItemRequest()
        ];

        if ($plan->type_plan === 'R') {
            switch ($plan->recurrence) {
                case 60: // bimestral => interval_count 2 interval month
                    $request->intervalCount = 2;
                    break;
                case 90: // trimestral => interval_count 3 interval month
                    $request->intervalCount = 3;
                    break;
                case 180: // semestral => interval_count 6 interval month
                    $request->intervalCount = 6;
                    break;
            }
        }

        // Plan Item 1
        $request->items[0]->name = Plan::PLAN_ITEM_CHARGE;
        $request->items[0]->quantity = 1;
        $request->items[0]->pricingScheme = new CreatePricingSchemeRequest();
        $request->items[0]->pricingScheme->price = str_replace('.', '', $plan->price);
        $request->items[0]->price = str_replace('.', '', $plan->price);

        // Plan Item 2
        $request->items[1]->name = Plan::PLAN_ITEM_REGISTRATION;
        $request->items[1]->cycles = 1;
        $request->items[1]->quantity = 1;
        $request->items[1]->pricingScheme = new CreatePricingSchemeRequest();
        $request->items[1]->pricingScheme->price = str_replace('.', '', $plan->setup_price);
        $request->items[1]->price = str_replace('.', '', $plan->setup_price);

        try {
            $result = $this->plansController->createPlan($request);
            $return  = json_encode($result, JSON_PRETTY_PRINT);

            $plan->integratable()->delete();
            $integration = Integration::where('id_integration', '=', Constants::CONSTANT_INTEGRATION_MUNDIPAGG)->where('platform_id', Auth::user()->platform_id)->first();
            $plan->integratable()->create(['integration_id' => $integration->id, 'integration_type_id' => $result->id]);

            return ['status' => 'success', 'data' => $return];
        } catch (\Exception $e) {
            return ['status' => 'error', 'data' => $e];
        }
    }

    public function updatePlan($plan, $status = null)
    {
        try {
            $mundiPlanId = $plan->integration->integration_type_id;
            $request = new UpdatePlanRequest();
            $request->name = $plan->name;
            $request->description = $plan->description;
            $request->currency = $plan->currency;
            $request->interval = Plan::getIntervalMundi($plan->recurrence);
            $request->intervalCount = $plan->charge_until;
            $request->minimumPrice = str_replace('.', '', $plan->price);

            $statusPlan = ($plan->status === 1) ? 'active' : 'inactive';

            $request->status = (isset($status) && 'deleted') ? $status : $statusPlan;

            $request->paymentMethods = ["credit_card"];
            $request->billingType = "exact_day";
            $request->billingDays = [(int) date('d', strtotime($plan->created_at))];
            $request->intervalCount = ($plan->charge_until > 0) ?$plan->charge_until : 1;

            $result = $this->plansController->updatePlan($mundiPlanId, $request);

            return ['status' => 'success', 'data' => $result];
        }
        catch( \Exception $e){
            return ['status' => 'error', 'data' => $e];
        }
    }

    public function deletePlan($plan)
    {
        $this->updatePlan($plan, 'deleted');

        try {
            $mundiPlanId = $plan->integration->integration_type_id;
            $result = $this->plansController->deletePlan($mundiPlanId);
            return ['status' => 'success', 'data' => $result];
        }
        catch (\Exception $e) {
            return ['status' => 'error', 'data' => $e];
        }
    }
}
