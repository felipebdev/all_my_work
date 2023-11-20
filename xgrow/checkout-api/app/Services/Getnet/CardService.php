<?php

namespace App\Services\Getnet;

use App\Constants;
use StdClass;
use Getnet\API\Token;
use Getnet\API\Transaction;
use Getnet\API\Getnet;
use App\PaymentCards;
use App\Plan;
use App\Platform;
use App\Subscriber;
use App\Integration;
use App\Subscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use App\Services\GetnetService;
use App\Services\Getnet\SafeService as GetnetSafeService;

class CardService extends Controller
{
    private $token;
    private $api;
    private $subscriber;
    private $plan;
    private $getnetSafeService;
    private $sellerId;
    private $headerApi;
    private $platform_id;
    private $getnet;

    public function __construct($platform_id)
    {
        $this->getnetSafeService = new GetnetSafeService($platform_id);
        $getnetApi = new GetnetService($platform_id);
        $this->api = $getnetApi->getApi();
        $this->sellerId = $getnetApi->getSellerId();
        $this->headerApi = $getnetApi->getHeaders();

        $this->subscriber = new Subscriber;
        $this->plan = new Plan;
        $this->platform_id = $platform_id;

        $this->getnet = new Getnet($getnetApi->getClientId(), $getnetApi->getSecretId(), config('getnet.' . config('app.env').'.environment'));

    }

    public function index()
    {
        $this->headerApi["Content-Type"] = "application/x-www-form-urlencoded";

        $response = $this->api->get('/v1/plans?page=1&limit=500', [
            "headers" => $this->headerApi
        ]);

        return json_decode($response->getBody())->plans;
    }

    public function tokenizationCard($data)
    {
        try {
            $this->headerApi["Content-Type"] = "application/json";

            $response = $this->api->request('POST', '/v1/tokens/card', [
                "headers" => $this->headerApi,
                "json" => $data
            ]);

            return ['status' => 'success', 'data' => json_decode($response->getBody(), true)];

        }
        catch( \Exception $e){
            $response = json_decode($e->getResponse()->getBody()->getContents(), false);
            return ['status' => 'error', 'data' => $response, 'message_request' => $e->getMessage()];
        }
    }

    public function store($data)
    {
        $subscriber = $this->subscriber->find($data['subscriber_id']);

        $integration = Integration::where('id_webhook', '=', 4)->first();

        $customerId = 0;

        foreach ($subscriber->integratable as $integratable) {
            if ($integratable->integration_id === $integration->id) {
                $customerId = (int) $integratable->integration_type_id;
            }
        }

        if ($customerId === 0) {
			$ret = new StdClass();
			$ret->message = 'Cliente não encontrado!';
			$ret->details = [];
            return ['status' => 'error', 'data' => $ret];
        }

        $plan = $this->plan->find(base64_decode($data['plan_id']));

        $numberCard = str_replace(' ', '', $data['cardholder_identification']);

        $forTokenCard = [
            "customer_id" => $customerId,
            "card_number" => $numberCard
        ];

        $response = $this->tokenizationCard($forTokenCard);

        if ($response['status'] !== 'success') {
			$ret = new StdClass();
			$ret->message = $response['data']['response'];
			$ret->details = [];
            return ['status' => 'error', 'data' => $ret];
        }

        $numberToken = $response['data']['number_token'];
        // Safe [cofre de cartões]
        $safe = $this->getnetSafeService->store($data, $numberToken, $customerId);

        if($safe['status'] === 'error') {
			$ret = new StdClass();
			$ret->message = $safe['message'];
			$ret->details = [];
            return ['status' => 'error', 'data' => $ret];
        }

        $subscription = new Subscription;

        $subscription->platform_id = $data['platform_id'];
        $subscription->plan_id = $plan->id;
        $subscription->subscriber_id = $subscriber->id;
        $subscription->gateway_transaction_id = '';

        $subscription->save();

        if ((int) $data['installment'] > 0) {

            if (!isset($data['course_id']) || $data['course_id'] <= 0) {
                $ret = new StdClass();
                $ret->message = "Checkout sem curso atribuído!";
                return ['status' => 'error', 'data' => $ret];
            }

            $expiration = explode("/", $data['expiration']);
            $amount = round($plan->price / $data['installment'], 2); // ver como será com juros ou não
            $platform = Platform::find($this->platform_id);

            $transactionType = ((int) $data['installment'] === 1) ? 'FULL' : 'INSTALL_NO_INTEREST';

            $name = explode(' ', $subscriber->name);
            $first_name = array_shift($name);
            $last_name = array_pop($name);
            $document_number = str_replace('/', '', str_replace('.', '', str_replace('-', '', $subscriber->document_number)));
            $phone_number = ($subscriber->main_phone != '') ? $subscriber->main_phone : $subscriber->cel_phone;

            $dataPayment = new StdClass;
            $dataPayment->amount = str_replace('.', '', $amount);
            $dataPayment->card_number = $numberCard;
            $dataPayment->customer_id = (string) $customerId;
            $dataPayment->soft_descriptor = $platform->name;
            $dataPayment->number_installments = $data['installment'];
            $dataPayment->transaction_type = $transactionType;
            $dataPayment->expiration_month = $expiration[0];
            $dataPayment->expiration_year = $expiration[1];
            $dataPayment->cardholder_name = $data['cardholder_name'];
            $dataPayment->security_code = $data['security_code'];
            $dataPayment->document_type = $subscriber->document_type;
            $dataPayment->customer_email = $subscriber->email;
            $dataPayment->first_name = $first_name;
            $dataPayment->last_name = $last_name;
            $dataPayment->customer_name = $subscriber->name;
            $dataPayment->phone_number = $phone_number;
            $dataPayment->document_number = $document_number;
            $dataPayment->postal_code = str_replace('-', '', $subscriber->address_zipcode);
            $dataPayment->address_city = $subscriber->address_city;
            $dataPayment->address_comp = $subscriber->address_comp;
            $dataPayment->address_country = $subscriber->address_country;
            $dataPayment->address_district = $subscriber->address_district;
            $dataPayment->address_number = $subscriber->address_number;
            $dataPayment->address_state = $subscriber->address_state;
            $dataPayment->address_street = $subscriber->address_street;
            $dataPayment->order_id = (string) $subscription->id;


//            $dataForm = [
//                "seller_id" => $this->sellerId,
//                "amount" => $amount,
//                "order" => [
//                    "order_id" => (string) $subscription->id,
//                    "product_type" => "service"
//                ],
//                "customer" => [
//                    "customer_id" => (string) $customerId,
//                    "first_name" => $first_name,
//                    "last_name" => $last_name,
//                    "email" => $subscriber->email,
//                    "document_type" => $subscriber->document_type,
//                    "document_number" => $document_number,
//                    "phone_number" => $phone_number,
//                    "billing_address" => [
//                        "street" => $subscriber->address_street,
//                        "number" => $subscriber->address_number,
//                        "complement" => $subscriber->address_comp,
//                        "district" => $subscriber->address_district,
//                        "city" => $subscriber->address_city,
//                        "state" => $subscriber->address_state,
//                        "country" => $subscriber->address_country,
//                        "postal_code" => str_replace('-', '', $subscriber->address_zipcode)
//                    ]
//                ],
//                "credit" =>  [
//                    "delayed" => false,
//                    "authenticated" => false,
//                    "pre_authorization" => false,
//                    "save_card_data" => false,
//                    "transaction_type" => $transactionType,
//                    "number_installments" => (int) $data['installment'],
//                    "soft_descriptor" => $platform->name,
//                    "card" =>  [
//                        "number_token" => $numberToken,
//                        "expiration_month" => $expiration[0],
//                        "expiration_year" => $expiration[1]
//                    ]
//                ]
//            ];

            try {
//                $this->headerApi["Content-Type"] = "application/json";
//                $response = $this->api->request('POST', '/v1/payments/credit', [
//                    "headers" => $this->headerApi,
//                    "json" => $dataForm
//                ]);
//                $return = json_decode($response->getBody(), true);

                $return = json_decode($this->paymentCardGetnet($dataPayment), true);

//                if (isset($return['status']) && $return['status'] === 'failed'){
//                    $subs = Subscription::where('id', '=', $subscription->id);
//                    $subs->update(['payment_pendent' => date_format(now(), 'Y-m-d')]);
//
//                    return response(['status' => 'error', 'message' => $return['status_details']]);
//                }

                if (isset($return['status_code']) && $return['status_code'] !== 200) {
                    $subs = Subscription::where('id', '=', $subscription->id);
                    $subs->update(['payment_pendent' => date_format(now(), 'Y-m-d')]);

                    $ret = new StdClass();
                    $ret->message = $return['message'];
                    $ret->details = $return['details'] ?? [];
                    return ['status' => 'error', 'data' => $ret];
                }

                PaymentCards::create([
                    "platform_id" => $this->platform_id,
                    "course_id" => $data['course_id'],
                    "subscriber_id" => $customerId,
                    "payment_id" => $return['payment_id'],
                    "seller_id" => $return['seller_id'],
                    "amount" => $return['amount'],
                    "currency" => $return['currency'],
                    "order_id" => $return['order_id'],
                    "status" => $return['status'],
                    "received_at" => $return['received_at'],
                    "credit_delayed" => $return['credit']['delayed'],
                    "credit_authorization_code" => $return['credit']['authorization_code'],
                    "credit_authorized_at" => $return['credit']['authorized_at'],
                    "credit_reason_code" => $return['credit']['reason_code'],
                    "credit_reason_message" => $return['credit']['reason_message'],
                    "credit_acquirer" => $return['credit']['acquirer'],
                    "credit_soft_descriptor" => $return['credit']['soft_descriptor'],
                    "credit_brand" => $return['credit']['brand'],
                    "credit_terminal_nsu" => $return['credit']['terminal_nsu'],
                    "credit_acquirer_transaction_id" => $return['credit']['acquirer_transaction_id'],
                    "credit_transaction_id" => $return['credit']['transaction_id']
                ]);

                $subscription = Subscription::find($subscription->id);

                $subscription->integratable()->delete();
                $integration = Integration::where('platform_id', $subscriber->platform_id)->where('id_integration', Constants::CONSTANT_INTEGRATION_GETNET)->first();
                $subscription->integratable()->create(['integration_id' => $integration->id, 'integration_type_id' => $return['payment_id']]);

                return ['status' => 'success', 'message' => 'Assinatura concluída com sucesso!', 'data' => $return];

            }
            catch( \Exception $e){
                $response = json_decode($e->getResponse()->getBody()->getContents(), false);
                return ['status' => 'error', 'data' => $response, 'message_request' => $e->getMessage()];
            }
        }


        return ['status' => 'success', 'message' => 'Assinatura concluída com sucesso!', 'data' => $safe];


//        $expiration = explode("/", $data['expiration']);

        // só deverá criar assinatura no getnet quando passar os dias gratuitos

//        $dataForm = [
//            "seller_id" => $this->sellerId,
//            "customer_id" => $subscriber->integration->integration_type_id,
//            "plan_id" => $plan->integration->integration_type_id,
//            "subscription" => [
//                "payment_type" => [
//                    "credit" => [
//                        "transaction_type" => "FULL",
//                        "number_installments" => 1,
//                        "card" => [
//                            "number_token" => $numberToken,
//                            "cardholder_name" => $data['cardholder_name'],
//                            "security_code" => $data['security_code'],
//                            "expiration_month" => $expiration[0],
//                            "expiration_year" => $expiration[1]
//                        ]
//                    ]
//                ]
//            ]
//        ];

//        try {
//            $this->headerApi["Content-Type"] = "application/json";
//
//            $response = $this->api->request('POST', '/v1/subscriptions', [
//                "headers" => $this->headerApi,
//                "json" => $dataForm
//            ]);
//
//            $return = json_decode($response->getBody(), true);
//
//            if ($return['status'] == 'failed') {
//                return response(['status' => 'error', 'message' => $return['status_details']]);
//            }
//
//            $subscription = new Subscription;
//
//            $subscription->platform_id = $data['platform_id'];
//            $subscription->plan_id = $plan->id;
//            $subscription->subscriber_id = $subscriber->id;
//            $subscription->gateway_transaction_id = $return['subscription']['subscription_id'];
//
//            $subscription->save();
//
//            $subscription->integratable()->delete();
//            $integration = Integration::where('id_integration', '=', 'GETNET')->first();
//            $subscription->integratable()->create(['integration_id' => $integration->id, 'integration_type_id' => $return['subscription']['subscription_id']]);
//
//            return response(['status' => 'success', 'message' => 'Assinatura concluída com sucesso!', 'data' => $return]);
//
//        }
//        catch( \Exception $e){
//            $response = json_decode($e->getResponse()->getBody()->getContents(), false);
//            return ['status' => 'error', 'data' => $response, 'message_request' => $e->getMessage()];
//        }
    }

    public function getCardData($cardId) {
        try {
            $this->headerApi["Content-Type"] = "application/json";

            $response = $this->api->get('/v1/cards/'.$cardId, [
                "headers" => $this->headerApi
            ]);

            $return = json_decode($response->getBody(), false);

            return ['status' => 'success', 'data' => $return];

        }
        catch( \Exception $e){
            $response = json_decode($e->getResponse()->getBody()->getContents(), false);

            $errors = '';

            if ((int) $response->status_code !== 200) {
                if (count($response->details)  > 0) {
                    foreach ($response->details as $item) {

                        $errors .= "{$item->description} [{$item->description_detail}] \n";
                    }
                }
            }

            return ['status' => 'error', 'data' => $response, 'message' => $errors, 'message_request' => $e->getMessage()];
        }
    }

    public function paymentCardGetnet($dataPayment)
    {


        // Autenticação da API (client_id, client_secret, env)
//        $getnet = new Getnet("c076e924-a3fe-492d-a41f-1f8de48fb4b1", "bc097a2f-28e0-43ce-be92-d846253ba748", "SANDBOX");
//        $this->getnet

        // Inicia uma transação
        $transaction = new Transaction();

        // Dados do pedido - Transação
        $transaction->setSellerId($this->sellerId);
        $transaction->setCurrency("BRL");
        $transaction->setAmount($dataPayment->amount);

        // Gera token do cartão - Obrigatório
        $card = new Token($dataPayment->card_number, $dataPayment->customer_id, $this->getnet);

        // Dados do método de pagamento do comprador
        $transaction->Credit("")
            ->setAuthenticated(false)
            ->setDynamicMcc("1799")
            ->setSoftDescriptor($dataPayment->soft_descriptor)
            ->setDelayed(false)
            ->setPreAuthorization(false)
            ->setNumberInstallments($dataPayment->number_installments)
            ->setSaveCardData(false)
            ->setTransactionType($dataPayment->transaction_type)
            ->Card($card)
            ->setBrand("MasterCard")
            ->setExpirationMonth($dataPayment->expiration_month)
            ->setExpirationYear($dataPayment->expiration_year)
            ->setCardholderName($dataPayment->cardholder_name)
            ->setSecurityCode($dataPayment->security_code);
        // Dados pessoais do comprador
        $transaction->Customer($dataPayment->customer_id)
            ->setDocumentType($dataPayment->document_type)
            ->setEmail($dataPayment->customer_email)
            ->setFirstName($dataPayment->first_name)
            ->setLastName($dataPayment->last_name)
            ->setName($dataPayment->customer_name)
            ->setPhoneNumber($dataPayment->phone_number)
            ->setDocumentNumber($dataPayment->document_number)
            ->BillingAddress($dataPayment->postal_code)
            ->setCity($dataPayment->address_city)
            ->setComplement($dataPayment->address_comp)
            ->setCountry($dataPayment->address_country)
            ->setDistrict($dataPayment->address_district)
            ->setNumber($dataPayment->address_number)
            ->setPostalCode($dataPayment->postal_code)
            ->setState($dataPayment->address_state)
            ->setStreet($dataPayment->address_street);
        // Dados de entrega do pedido
        $transaction->Shippings("")
            ->setEmail($dataPayment->customer_email)
            ->setFirstName($dataPayment->first_name)
            ->setName($dataPayment->customer_name)
            ->setPhoneNumber($dataPayment->phone_number)
            ->ShippingAddress($dataPayment->postal_code)
            ->setCity($dataPayment->address_city)
            ->setComplement($dataPayment->address_comp)
            ->setCountry($dataPayment->address_country)
            ->setDistrict($dataPayment->address_district)
            ->setNumber($dataPayment->address_number)
            ->setPostalCode($dataPayment->postal_code)
            ->setState($dataPayment->address_state)
            ->setStreet($dataPayment->address_street);
        // Detalhes do Pedido
        $transaction->Order($dataPayment->order_id)
            ->setProductType("service")
            ->setSalesTax("0");
        $transaction->setSellerId($this->sellerId);
        $transaction->setCurrency("BRL");
        $transaction->setAmount($dataPayment->amount);

        // FingerPrint - Antifraude
        $transaction->Device("hash-device-id")->setIpAddress($_SERVER["REMOTE_ADDR"]);

        // Processa a Transação
        $response = $this->getnet->Authorize($transaction);

        // Resultado da transação - Consultar tabela abaixo

        return $response->getResponseJSON();
    }
}
