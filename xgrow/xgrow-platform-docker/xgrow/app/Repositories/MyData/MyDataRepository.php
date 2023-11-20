<?php

namespace App\Repositories\MyData;

use App\Client;
use App\Mail\SendMailAuthorizationToken;
use App\PlatformUser;
use App\Producer;
use App\Services\Checkout\CheckoutBaseService;
use App\Services\EmailService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class MyDataRepository
{
    protected $clientModel;
    protected $producerModel;
    protected $emailService;
    protected $checkoutBaseService;

    public function __construct(Client $client, Producer $producer, EmailService $emailService, CheckoutBaseService $checkoutBaseService)
    {
        $this->clientModel = $client;
        $this->producerModel = $producer;
        $this->emailService = $emailService;
        $this->checkoutBaseService = $checkoutBaseService;
    }

    public function getAddress($userEmail): array
    {
        $data = $this->clientModel
            ->select(
                'address',
                'number',
                'complement',
                'district',
                'city',
                'state',
                'zipcode'
            )
            ->where('email', $userEmail)->first();

        if(is_null($data))
            return [];

        return $data->toArray();

    }

    public function updateAddress(array $request, $userEmail): array
    {

        $data = $this->clientModel
            ->where('email', $userEmail)
            ->first();

        if(is_null($data))
            return ['message' => 'Endereço não encontrado!', 'data' => $data];

        $data->update(
                [
                    'address' => $request['address'],
                    'number' => $request['number'],
                    'complement' => isset($request['complement'])? $request['complement'] : '',
                    'district' => $request['district'],
                    'city' => $request['city'],
                    'state' => $request['state'],
                    'zipcode' => $request['zipcode']
                ]
            );

        return ['message' => 'Endereço atualizado com sucesso!', 'data' => $data];
    }

    public function getBankDetails()
    {
        $user = Auth::user();
        $req = $this->checkoutBaseService->connectionConfig(null, $user->id);

        $res = $req->get('bank-account');
        return json_decode($res->getBody());
    }

    public function getBankDetailsFromClients()
    {
        $user = Auth::user();

        $client = Client::select()->where('email', $user->email)->first();

        $data = [];

        if (!empty($client)) {
            $data['object'] = "bank_account";
            $data['bank_code'] = $client->bank;
            $data['agency'] = $client->branch;
            $data['agency_digit'] = $client->branch_check_digit;
            $data['account'] = $client->account;
            $data['account_digit'] = $client->account_check_digit;
            $data['account_type'] = $client->account_type;
            $data['document_type'] = ($client->type_person == 'F') ? 'CPF' : 'CNPJ';
            $data['document_number'] = ($client->type_person == 'F') ? $client->cpf : $client->cnpj;
            $data['legal_name'] =  $client->holder_name;
            $data['email'] = $client->email;
        }

        return $data;

    }

    public function sendAuthorizationToken()
    {
        $two_factor_code = rand(100000, 999999);
        Session::put('two_factor_code', $two_factor_code);
        Mail::to(Auth::user()->email)
                ->send(new SendMailAuthorizationToken($two_factor_code));
    }

    public function verifyAuthorizationToken(int $two_factor_code)
    {
            if(Session::get('two_factor_code') !== $two_factor_code){
                return false;
            }
            return true;
    }

    public function resetAuthorizationToken()
    {
        Session::forget('two_factor_code');
    }

    public function updateBankDetails(array $data)
    {
        $user = Auth::user();
        $req = $this->checkoutBaseService->connectionConfig(null, $user->id);
        $res = $req->put('bank-account', [
            'json' => [
                "bank_code" => $data['bank_code'],
                "object" => "bank_account",
                "agency" => $data['agency'],
                "agency_digit" => $data['agency_digit'],
                "account" => $data['account'],
                "account_digit" => $data['account_digit'],
                "account_type" => $data['account_type'],
                "document_type" => (strlen($data['document_number']) > 11) ? 'cnpj' : 'cpf',
                "document_number" => $data['document_number'],
                "legal_name" => $data['legal_name']
            ]
        ]);
        return $res;
    }

    public function createBankDetails(array $data)
    {
        $user = Auth::user();
        $req = $this->checkoutBaseService->connectionConfig(null, $user->id);
        $res = $req->post('bank-account', [
            'json' => [
                "bank_code" => $data['bank_code'],
                "object" => "bank_account",
                "agency" => $data['agency'],
                "agency_digit" => $data['agency_digit'],
                "account" => $data['account'],
                "account_digit" => $data['account_digit'],
                "account_type" => $data['account_type'],
                "document_type" => $data['document_type'],
                "document_number" => $data['document_number'],
                "legal_name" => $data['legal_name']
            ]
        ]);
        return $res;
    }

    public function getIdentity($userEmail): array
    {
        $data = $this->clientModel
            ->select(
                'first_name',
                'last_name',
                'fantasy_name',
                'company_name',
                'type_person',
                'cpf',
                'cnpj',
                'verified',
                'check_document_type',
                'document_front_image_url'
            )
            ->where('email', $userEmail)->first();

        if(is_null($data))
            return [];

        return $data->toArray();

    }

    public function updateIdentity($request, $imageUrl): array
    {

        $client = $this->clientModel->where('email', Auth::user()->email)->first();
        if (!$client) {
            return [
                'error' => true,
                'message' => 'Usuário possui cadastro incompleto!',
                'code' => 422
            ];
        }

        if($request->type_person == 'F') {
            $data = [
                'type_person' => 'F',
                'cpf' => $request->document ?? null,
                'first_name' => $request->first_name ?? null,
                'last_name' => $request->last_name ?? null,
                'cnpj' => null,
                'company_name' => '',
                'verified' => 1,
                'check_document_type' => 1,
                'document_front_image_url' => $imageUrl
            ];
        } else {
            $data = [
                'type_person' => 'J',
                'cpf' => null,
                'cnpj' => $request->document ?? null,
                'company_name' => $request->company_name ?? null,
                'verified' => 1,
                'check_document_type' => 1,
                'document_front_image_url' => $imageUrl
            ];
        }
        $client->update($data);

        Producer::where('platform_user_id', PlatformUser::where('email', Auth::user()->email)->first()->id)
            ->update(['document_verified' => 1]);

            return [
                'error' => false,
                'message' => 'Dados atualizados com sucesso.',
                'code' => 200
            ];

    }

    /**
     * @param $userEmail
     * @return array|void
     * @throws Exception
     */
    public function getFees($userEmail)
    {
        $percent_split = Client::PERCENT_SPLIT;
        $tax_transaction = Client::TAX_TRANSACTION;

        $client = $this->clientModel
            ->select('percent_split', 'tax_transaction')
            ->where('email', $userEmail)
            ->first();

        if($client){
            $percent_split = $client->percent_split;
            $tax_transaction = $client->tax_transaction;
        }

        return [
           'percent_split' => $percent_split,
           'tax_transaction' => $tax_transaction
        ];
    }

}
