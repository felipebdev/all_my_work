<?php

namespace App\Services\Mundipagg;

use MundiAPILib\Models\CreateCustomerRequest;
use MundiAPILib\Models\CreateSubscriptionRequest;
use MundiAPILib\Models\CreateAddressRequest;
use MundiAPILib\Models\CreateCardRequest;
use App\Integration;
use App\Services\MundipaggService;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use App\Services\GetnetService;
use App\Constants;
use App\Services\Getnet\CardService as GetnetCardService;
use StdClass;

class SubscriptionService extends Controller
{

    private $sellerId;
    private $api;
    private $getnetCardService;
    private $getnetService;
    private $subscriptionController;

    public function __construct($platform_id)
    {
//        $this->getnetCardService = new GetnetCardService($platform_id);
//
//        $this->getnetService = new GetnetService($platform_id);
//        $this->api = $this->getnetService->getApi();
//        $this->headerApi = $this->getnetService->getHeaders();


        $mundipaggService = new MundipaggService($platform_id);
        $client = $mundipaggService->getClient();
        $this->customerController = $client->getCustomers();
        $this->subscriptionController = $client->getSubscriptions();
        $this->platformId = $platform_id;


    }

    public function index()
    {
//        $this->headerApi["Content-Type"] = "application/x-www-form-urlencoded";
//
//        $response = $this->api->get('/v1/subscriptions?page=1&limit=500', [
//            "headers" => $this->headerApi
//        ]);
//
//        return json_decode($response->getBody())->subscriptions;
    }

    public function getSubscription($subscriptionId)
    {
//        try {
//            $this->headerApi["Content-Type"] = "application/x-www-form-urlencoded";
//
//            $response = $this->api->get('/v1/subscriptions/'.$subscriptionId, [
//                "headers" => $this->headerApi
//            ]);
//
//            return json_decode($response->getBody());
//        }
//        catch( \Exception $e){
//            $response = json_decode($e->getResponse()->getBody()->getContents(), false);
//            return ['status' => 'error', 'data' => $response, 'message_request' => $e->getMessage()];
//        }

    }

    public function store($formData)
    {

        $body = new CreateSubscriptionRequest();

        $body->code = $formData->code;
        $body->planId = $formData->plan_id;
        $body->paymentMethod = $formData->payment_method;
        $body->customerId = $formData->customer_id;

//        $body->customer = new CreateCustomerRequest();
//        $body->customer->id = $formData->customer_id;
//        $body->customer->name = $formData->customer_name;

        $body->card = new CreateCardRequest();
        $body->card->number = $this->prepareCardNumber($formData->card->number);
        $body->card->holderName = $formData->card->holderName;
        $body->card->holderDocument = $this->prepareHolderDocument($formData->card->holderDocument);
        $body->card->expMonth = $formData->card->expMonth;
        $body->card->expYear = $formData->card->expYear;
        $body->card->cvv = $formData->card->cvv;

        $body->billinAddress = new CreateAddressRequest();
        $body->billinAddress->line1 = $formData->billinAddress->line1;
        $body->billinAddress->line2 = $formData->billinAddress->line2;
        $body->billinAddress->zipCode = $formData->billinAddress->zipCode;
        $body->billinAddress->city = $formData->billinAddress->city;
        $body->billinAddress->state = $formData->billinAddress->state;
        $body->billinAddress->country = "BR";


        try {
            $result = $this->subscriptionController->createSubscription($body);
//            dd($result);
//            $return  = json_encode($result, JSON_PRETTY_PRINT);
            return ['status' => 'success', 'data' => $result];

        } catch (\Exception $e) {

            return ['status' => 'error', 'data' => $e];
        }


//        $card = $this->getnetCardService->getCardData($data->safe_integration);
//
//        if($card['status'] === 'error') {
//            return $card;
//        }
//
//        $sellerId = $this->getnetService->getSellerId();
//        $dataForm = [
//            "seller_id" => $sellerId,
//            "customer_id" => $data->subscriber_integration,
//            "plan_id" => $data->plan_integration,
//            "subscription" => [
//                "payment_type" => [
//                    "credit" => [
//                        "transaction_type" => "FULL",
//                        "number_installments" => 1,
//                        "card" => [
//                            "number_token" => $card['data']->number_token,
//                            "cardholder_name" => $card['data']->cardholder_name,
//                            "expiration_month" => str_pad($card['data']->expiration_month, '2', '0', STR_PAD_LEFT),
//                            "expiration_year" => $card['data']->expiration_year
//                        ]
//                    ]
//                ]
//            ]
//        ];
//
//        try {
//            $this->headerApi["Content-Type"] = "application/json";
//
//            $response = $this->api->request('POST', '/v1/subscriptions', [
//                "headers" => $this->headerApi,
//                "json" => $dataForm
//            ]);
//
//            $return = json_decode($response->getBody(), false);
//
//            if ($return->status == 'failed') {
//                $subscription->update(['canceled_at' => date(now())]);
//                return ['status' => 'error', 'message' => $return->status_details];
//            }
//
//            if ($subscription->freedays_type !== 'free') {
//                $dataPayment = new StdClass;
//                $dataPayment->day = substr($subscription->created_at,8, 2);
//                $dataPayment->subscription_id = $return->subscription->subscription_id;
//
//                $responseDay = $this->paymentDateSubscription($dataPayment);
//
//                if ($responseDay['status'] === 'error') {
//                    return ['status' => 'error', 'data' => $responseDay, 'message' => $subscription['data']['response']];
//                }
//            }
//
//            $subscription->update(['gateway_transaction_id' => $return->subscription->subscription_id]);
//
//            return ['status' => 'success', 'data' => $return];
//
//        }
//        catch (\GuzzleHttp\Exception\ServerException $e) {
//            $response = json_decode($e->getResponse()->getBody()->getContents(), false);
//            return ['status' => 'error', 'data' => $response, 'message_request' => $e->getMessage()];
//        }
//        catch (\GuzzleHttp\Exception\ClientException $e) {
//            $response = json_decode($e->getResponse()->getBody()->getContents(), false);
//            return ['status' => 'error', 'data' => $response, 'message_request' => $e->getMessage()];
//        }
//        catch (\GuzzleHttp\Exception\ConnectException $e) {
//            $response = json_decode($e->getResponse()->getBody()->getContents(), false);
//            return ['status' => 'error', 'data' => $response, 'message_request' => $e->getMessage()];
//        }
//        catch (\GuzzleHttp\Exception\TooManyRedirectsException $e) {
//            $response = json_decode($e->getResponse()->getBody()->getContents(), false);
//            return ['status' => 'error', 'data' => $response, 'message_request' => $e->getMessage()];
//        }
//        catch( \Exception $e){
//            return ['status' => 'error', 'data' => ['message' => $e->getMessage(), 'code' => $e->getCode()]];
//        }
    }

    public function cancelSubscription($data)
    {
        $sellerId = $this->getnetService->getSellerId();
        $dataForm = [
            'seller_id' =>  $sellerId,
            'status_details' => $data->status_details
        ];

        try {
            $this->headerApi["Content-Type"] = "application/json";

            $response = $this->api->request('POST', '/v1/subscriptions/' . $data->subscription_id . '/cancel', [
                "headers" => $this->headerApi,
                "json" => $dataForm
            ]);

            $return = json_decode($response->getBody(), true);

            return ['status' => 'success', 'data' => $return];

        }
        catch( \Exception $e){
            $response = json_decode($e->getResponse()->getBody()->getContents(), false);
            return ['status' => 'error', 'data' => $response, 'message_request' => $e->getMessage()];
        }
    }

    public function getProjection($subscriptionId)
    {
        $this->headerApi["Content-Type"] = "application/x-www-form-urlencoded";

        $response = $this->api->get('/v1/subscriptions/'.$subscriptionId.'/charges/projection', [
            "headers" => $this->headerApi
        ]);

        return json_decode($response->getBody());
    }

    public function paymentDateSubscription($data)
    {
        $dataForm = ['day' => $data->day];

        try {
            $this->headerApi["Content-Type"] = "application/json";

            $response = $this->api->request('PATCH', '/v1/subscriptions/' . $data->subscription_id . '/paymentDate', [
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

    public function paymentTypeCreditCardSubscription($data)
    {
     /* $data deve conter:
      "subscription_id" => "1c69c10b-8ed0-47ad-a44b-90b73c164ef8"
      "card_number" => "5155901222280001"
      "cardholder_name" => "JOAO DA SILVA"
      "expiration_month" => "12"
      "expiration_year" => "20"
     */

        $subscriptionData = $this->getSubscription($data->subscription_id);

        if ($subscriptionData->status !== 'success') {
            return response()->json(['status' => 'error', 'data' => $data]);
        }

        $forTokenCard = [
            "customer_id" => $subscriptionData->customer->customer_id,
            "card_number" => $data->card_number
        ];

        $response = $this->getnetCardService->tokenizationCard($forTokenCard);

        if ($response['status'] != 'success') {
            return response()->json(['status' => 'error', 'data' => $response]);
        }

        $dataForm = [
            'number_token' => $response['data']['number_token'],
            'cardholder_name' => $data->cardholder_name,
            'expiration_month' => $data->expiration_month,
            'expiration_year' => $data->expiration_year,
            'bin' => substr($data->card_number,0, 6)
        ];

        try {
            $this->headerApi["Content-Type"] = "application/json";

            $response = $this->api->request('PATCH', '/v1/subscriptions/' . $data->subscription_id . '/paymentType/credit/card', [
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

    public function importCharges($params = false)
    {
        $this->headerApi["Content-Type"] = "application/x-www-form-urlencoded";

        if (!isset($params)) {
            $params = [
                'page' =>  1,
                'limit' => 500
            ];
        }

        $response = $this->api->get('/v1/charges?page=' . $params['page'] . '&limit=' . $params['limit'], [
            "headers" => $this->headerApi
        ]);

        return json_decode($response->getBody());
    }

    private function prepareHolderDocument($number)
    {
        return str_replace('/', '', str_replace('-', '', str_replace('.', '', $number)));
    }

    private function prepareCardNumber($number)
    {
        return str_replace(' ', '', $number);
    }

}
