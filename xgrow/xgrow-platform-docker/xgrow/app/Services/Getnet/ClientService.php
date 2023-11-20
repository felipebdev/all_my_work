<?php

namespace App\Services\Getnet;

use App\Integration;
use App\Subscriber;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use App\Services\GetnetService;
use App\Constants;

class ClientService extends Controller
{

    private $api;
    private $sellerId;
    private $headerApi;
    private $token;
    private $platform_id;

    public function __construct($platform_id)
    {
        $getnetService = new GetnetService($platform_id);
        $this->api = $getnetService->getApi();
        $this->headerApi = $getnetService->getHeaders();
        $this->sellerId = $getnetService->getSellerId();
        $this->token = $getnetService->getToken();
        $this->platform_id = $platform_id;
    }

    public function index()
    {
        $this->headerApi["Content-Type"] = "application/x-www-form-urlencoded";

        $response = $this->api->get('/v1/customers?page=1&limit=500', [
            "headers" => $this->headerApi
        ]);

        return json_decode($response->getBody())->customers;
    }

    public function getCustomer($customerId)
    {
        $this->headerApi["Content-Type"] = "application/x-www-form-urlencoded";

        $response = $this->api->get('/v1/customers/'.$customerId, [
            "headers" => $this->headerApi
        ]);

        return json_decode($response->getBody());
    }

    public function store($subscriber)
    {
        $name = explode(" ", $subscriber->name);

        $indexSurname = count($name) - 1;

        $surname = ($indexSurname == 0) ? "" :  $name[$indexSurname];

        $dataForm = [
            "seller_id" => $this->sellerId,
            "customer_id" => $subscriber->id,
            "first_name" => $name[0],
            "last_name" => $surname,
            "document_type" => $subscriber->document_type,
            "document_number" => $subscriber->document_number
        ];

        try {
            $this->headerApi["Content-Type"] = "application/json";

            $response = $this->api->request('POST', '/v1/customers', [
                "headers" => $this->headerApi,
                "json" => $dataForm
            ]);

            $return = json_decode($response->getBody(), true);

            $subscriber->integratable()->delete();
            $integration = Integration::where('platform_Id', $subscriber->platform_id)->where('id_integration', '=', Constants::CONSTANT_INTEGRATION_GETNET)->first();
            $subscriber->integratable()->create(['integration_id' => $integration->id, 'integration_type_id' => $return['customer_id']]);

            return ['status' => 'success', 'data' => json_decode($response->getBody(), false)];
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

    public function integrate($subscriber)
    {

    }
}
