<?php

namespace App\Services\Getnet;

use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use App\Services\GetnetService;

class SaleService extends Controller
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

    public function cancel($payment_id)
    {
        $dataForm = [];

        try {
            $this->headerApi["Content-Type"] = "application/json";

            $response = $this->api->request('POST', '/v1/payments/credit/' . $payment_id . '/cancel', [
                "headers" => $this->headerApi,
                "json" => $dataForm
            ]);

            $return = json_decode($response->getBody(), true);

            return ['status' => 'success', 'data' => $return];

        } catch (\GuzzleHttp\Exception\ServerException $e) {
            $response = json_decode($e->getResponse()->getBody()->getContents(), false);
            return ['status' => 'error', 'data' => $response, 'message_request' => $e->getMessage()];
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = json_decode($e->getResponse()->getBody()->getContents(), false);
            return ['status' => 'error', 'data' => $response, 'message_request' => $e->getMessage()];
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            $response = json_decode($e->getResponse()->getBody()->getContents(), false);
            return ['status' => 'error', 'data' => $response, 'message_request' => $e->getMessage()];
        } catch (\GuzzleHttp\Exception\TooManyRedirectsException $e) {
            $response = json_decode($e->getResponse()->getBody()->getContents(), false);
            return ['status' => 'error', 'data' => $response, 'message_request' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['status' => 'error', 'data' => ['message' => $e->getMessage(), 'code' => $e->getCode()]];
        }

    }
}
