<?php

namespace App\Services\Getnet;

use Auth;
use App\Integration;
use App\Plan;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use App\Services\GetnetService;

class PlanService extends Controller
{
    private $api;
    private $headerApi;
    private $sellerId;

    public function __construct($platform_id)
    {
        $getnetService = new GetnetService($platform_id);
        $this->sellerId = $getnetService->getSellerId();
        $this->api = $getnetService->getApi();
        $this->headerApi = $getnetService->getHeaders();
    }

    public function index()
    {
        $this->headerApi["Content-Type"] = "application/x-www-form-urlencoded";

        $response = $this->api->get('/v1/plans?page=1&limit=500', [
            "headers" => $this->headerApi
        ]);

        return json_decode($response->getBody())->plans;
    }

    public function store($dados, $plan)
    {
        $type = Plan::getType($dados->recurrence);

        $billing_cycle = $specific_cycle_in_days = 0;

        if ($type === "specific") {
            $specific_cycle_in_days = $dados->recurrence;
        }

        if ($dados->charge_until !== '0') {
            $billing_cycle = Plan::getBillingCycle($dados->recurrence);
        }

        $period = [
            "type" => $type,
            "billing_cycle" => $billing_cycle,
            "specific_cycle_in_days" => $specific_cycle_in_days
        ];

        $amount = number_format($plan->price,2,'.',2);

        $dataForm = [
            "seller_id" => $this->sellerId,
            "name" => $dados->name,
            "amount" => str_replace(',', '', str_replace('.', '', $amount)),
            "currency" => $dados->currency,
            "payment_types" => [
                "credit_card"
            ],
            "period" => $period
        ];

        try {
            $this->headerApi["Content-Type"] = "application/json";

            $response = $this->api->request('POST', '/v1/plans', [
                "headers" => $this->headerApi,
                "json" => $dataForm
            ]);

            $return = json_decode($response->getBody(), true);

            $plan->integratable()->delete();
            $integration = Integration::where('id_integration', '=', 'GETNET')->where('platform_id', Auth::user()->platform_id)->first();
            $plan->integratable()->create(['integration_id' => $integration->id, 'integration_type_id' => $return['plan_id']]);

            return ['status' => 'success', 'data' => $return];

        }
        catch (\GuzzleHttp\Exception\ServerException $e) {
            $response = json_decode($e->getResponse()->getBody()->getContents(), false);
            return ['status' => 'error', 'data' => $response, 'message_request' => $e->getMessage()];
        }
        catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = json_decode($e->getResponse()->getBody()->getContents(), false);
            return ['status' => 'error', 'data' => $response, 'message_request' => $e->getMessage()];
        }
        catch (\GuzzleHttp\Exception\ConnectException $e) {
            $response = json_decode($e->getResponse()->getBody()->getContents(), false);
            return ['status' => 'error', 'data' => $response, 'message_request' => $e->getMessage()];
        }
        catch (\GuzzleHttp\Exception\TooManyRedirectsException $e) {
            $response = json_decode($e->getResponse()->getBody()->getContents(), false);
            return ['status' => 'error', 'data' => $response, 'message_request' => $e->getMessage()];
        }
        catch( \Exception $e){
            return ['status' => 'error', 'data' => ['message' => $e->getMessage(), 'code' => $e->getCode()]];
        }
    }

    public function getPlan($planId)
    {
        $this->headerApi["Content-Type"] = "application/x-www-form-urlencoded";

        $response = $this->api->get('/v1/plans/'.$planId, [
            "headers" => $this->headerApi
        ]);

        return json_decode($response->getBody());
    }

    public function updatePlan($planId, $name, ?string $description)
    {
        $dataForm = [
            'name' => $name,
            'description' => $description
        ];

        try {
            $this->headerApi["Content-Type"] = "application/json";

            $response = $this->api->request('PATCH', '/v1/plans/' . $planId, [
                "headers" => $this->headerApi,
                "json" => $dataForm
            ]);

            return ['status' => 'success', 'data' => ['dados' => json_decode($response->getBody(), true)]];

        }
        catch( \Exception $e){
            $response = json_decode($e->getResponse()->getBody()->getContents(), false);
            return ['status' => 'error', 'data' => $response, 'message_request' => $e->getMessage()];

        }
    }

    /**
     * @param $planId
     * @param $status
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateStatusPlan($planId, $status)
    {
        $dataForm = [
            'status' => $status
        ];

        try {
            $this->headerApi["Content-Type"] = "application/json";

            $response = $this->api->request('PATCH','/v1/plans/'.$planId.'/status/'.$status, [
                "headers" => $this->headerApi,
                "json" => $dataForm
            ]);

            return ['status' => 'success', 'data' => ['dados' => json_decode($response->getBody(), true)]];

        }
        catch( \Exception $e){
            $response = json_decode($e->getResponse()->getBody()->getContents(), false);
            return ['status' => 'error', 'data' => $response, 'message_request' => $e->getMessage()];
        }
    }
}
