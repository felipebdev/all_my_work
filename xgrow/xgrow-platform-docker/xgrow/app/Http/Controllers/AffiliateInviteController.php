<?php

namespace App\Http\Controllers;

use App\AffiliationSettings;
use App\Client;
use App\Http\Traits\CustomResponseTrait;
use App\Producer;
use App\ProducerProduct;
use App\Product;
use App\Services\Affiliate\AffiliationContractService;
use App\Services\Auth\ClientStatus;
use App\Services\Checkout\RecipientsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AffiliateInviteController extends Controller
{

    use CustomResponseTrait;

    private AffiliationContractService $affiliationContractService;
    private RecipientsService $recipientsService;

    public function __construct(AffiliationContractService $affiliationsRepository, RecipientsService $recipientsService)
    {
        $this->affiliationContractService = $affiliationsRepository;
        $this->recipientsService = $recipientsService;
    }

    public function affiliateInvite(Request $request)
    {
        if(!isset($request->invite)){
            return abort(404);
        }

        $user = Auth::user();
        $invite_token = $request->invite;
        $invite = $this->getInviteInfo($invite_token);

        if(is_null($invite)){
            return abort(404);
        }

        $product = $this->getProductInfo($invite->product_id);

        if(!$product->affiliation_enabled){
            return abort(404);
        }

        if ($this->affiliationContractService->isUserBlocked($user->id, $product->id)) {
            return abort(403, 'Você está bloqueado para afiliações na plataforma!');
        }

        $price = ($product->price * $invite->commission) / 100;
        $is_affiliate = $this->isAffiliate($user->id, $product->id);

        $status = ClientStatus::withoutPlatform(Auth::user()->email);

        return view('affiliates.invite', [
            'is_affiliate' => $is_affiliate,
            'product' => $product,
            'invite' => $invite,
            'price' => $price,
            'client_approved' => $status->clientApproved,
        ]);
    }

    protected function getInviteInfo($invite_token)
    {
        return AffiliationSettings::where('invite_link', $invite_token)->first();
    }

    protected function getProductInfo($product_id)
    {
        return Product::select('products.id',
                                'products.name',
                                'products.description',
                                'products.type',
                                'files.filename',
                                'plan_categories.name as category_name',
                                'plans.price',
                                'clients.first_name',
                                'clients.last_name',
                                'products.platform_id',
                                'products.affiliation_enabled',
                                'affiliation_settings.approve_request_manually')

           ->join('plans', 'plans.product_id', '=', 'products.id')
            ->join('platforms', 'platforms.id', '=', 'products.platform_id')
            ->join('clients', 'clients.id', '=', 'platforms.customer_id')
            ->leftJoin('plan_categories', 'plan_categories.id', '=', 'products.category_id')
            ->leftJoin('files', 'files.id', '=', 'products.image_id')
            ->leftJoin('affiliation_settings', 'affiliation_settings.product_id', '=', 'products.id')
            ->where('products.id', $product_id)
            ->where('plans.status', true)
            ->where('plans.deleted_at', null)
            ->orderBy('plans.price', 'DESC')
            ->first();
    }

    public function affiliationConfirm(Request $request)
    {
        Log::info("degub_affiliate : clicou no link.");

        $user = Auth::user();
        $invite = $this->getInviteInfo($request->affiliation_settings_id);
        $product = $this->getProductInfo($invite->product_id);

        if ($this->affiliationContractService->isUserBlocked($user->id, $product->id)) {
            Log::info("degub_affiliate : Afiliado bloqueado na plataforma.");
            return response()->json([
                'error' => true,
                'message' => 'Você está bloqueado para afiliações na plataforma!',
                'data' => []
            ]);
        }
        $is_affiliate = $this->isAffiliate($user->id, $product->id);
        if($is_affiliate){
            $message = 'Usuário já é afiliado ao produto!';
            Log::info("degub_affiliate : Ja é afiliado do produto.");
            return $this->customJsonResponse($message, 409, []);
        }

        if($this->isSamePlatform($user->id, $product->platform_id)){
            Log::info("degub_affiliate : ja é co-produtor na mesma plataforma.");
            return response()->json([
                'error' => true,
                'message' => 'Você não pode ser co-produtor e afiliado na mesma plataforma!',
                'data' => []
            ]);
        }

        $producers = $this->isCoproducer($user->id, $product->id);

        if($producers){
            Log::info("degub_affiliate : ja é co-produtor.", [$producers]);
            return response()->json([
                'error' => true,
                'message' => 'Você já é coprodutor do produto!',
                'data' => ['data' => $producers]
            ]);
        }

        $client = Client::where('email', Auth::user()->email)->first();

        $create_response = $this->createAffiliate($product, $client, $invite);
        Log::info("degub_affiliate : resposta final.", [$create_response]);
        return response()->json($create_response);
    }

    protected function isAffiliate($user_id, $product_id)
    {
        $is_affiliate = DB::table('producers')
            ->select('producer_products.status')
            ->join('producer_products', 'producer_products.producer_id', '=', 'producers.id')
            ->where([
                ['producers.type', 'A'],
                ['producers.platform_user_id', $user_id],
                ['producer_products.product_id', $product_id],
            ])
            ->whereIn('producer_products.status', ['active', 'pending', 'recipient_failed'])
            ->first();

        return $is_affiliate;
    }

    protected function isCoproducer($user_id, $product_id)
    {
        $producers = DB::table('producers')
            ->join('producer_products', 'producer_products.producer_id', '=', 'producers.id')
            ->where([ ['producers.platform_user_id', $user_id], ['producers.type', 'P'], ['producer_products.product_id', $product_id], ['producer_products.status', 'active'] ])
            ->first();

        return $producers;
    }

    protected function isSamePlatform($user_id, $platform_id)
    {
        $producers = DB::table('producers')
            ->where([ ['producers.platform_user_id', $user_id], ['producers.platform_id', $platform_id], ['producers.type', 'P'] ])
            ->first();

        return $producers;
    }

    public function createAffiliate($product, $client, $invite)
    {
        Log::info("degub_affiliate : criando afiliado.");
        try {
            $data_producer = [
                'type' => 'A',
                'platform_id' => $product->platform_id,
                'platform_user_id' => Auth::user()->id,
                'accepted_terms' => 1,
                'document_type' => ($client->type_person == 'F')? 'CPF' : 'CNPJ',
                'document' => ($client->type_person == 'F')? $client->cpf : $client->cnpj,
                'holder_name' => $client->holder_name,
                'account_type' => $client->account_type,
                'bank' => $client->bank,
                'branch' => $client->branch,
                'branch_check_digit' => $client->branch_check_digit,
                'account' => $client->account,
                'account_check_digit' => $client->account_check_digit,
                'document_verified' => 1,
            ];

            $producer = $this->getProducerOrCreate($data_producer);

            Log::info("degub_affiliate : criando producers.", [$producer]);
            if($producer){
                $recipientExists = is_null($producer->recipient_id)? false : true;
                Log::info("degub_affiliate : recipientExists?", [$recipientExists]);
                if (!$recipientExists) {
                    $recipient = $this->recipientsService->createProducerRecipient($producer->platform_id, 'affiliate');
                    $hasCreationError = $recipient['error'] ?? false;
                    $recipientExists = !$hasCreationError;

                    Log::info("Producer ID: ".$producer->id." Recebedor gerado: ".$recipientExists, ['recipient' => $recipient]);
                }
                Log::info("Producer ID: ".$producer->id." Recebedor gerado: ".$recipientExists." Recebedor existente: ".$producer->recipient_id);
                $producerProductStatus = $product->approve_request_manually ? 'pending' : 'active';
                $producerProduct = ProducerProduct::create(
                    [
                        'producer_id' => $producer->id,
                        'product_id' => $product->id,
                        'percent' => $invite->commission,
                        'status' => $recipientExists ? $producerProductStatus : 'recipient_failed',
                        'split_invoice' => 0
                    ]
                );

                Log::info("Criando o producer product! ID: ".$product->id);
                if(!$recipientExists){
                    return [
                        'error' => true,
                        'message' => 'Falha ao cadastrar afiliado! O recebedor não foi criado!',
                        'data' => ['Falha ao criar recipient_id!']
                    ];
                }
                Log::info("Tudo criado com sucesso!", [$producer]);

                if($product->approve_request_manually){
                    return [
                        'error' => false,
                        'message' => 'A sua solicitação foi enviada e esta pendente de aprovação, você pode acompanhar a aprovação pela área de afiliados.',
                        'data' => []
                    ];
                }

                return [
                    'error' => false,
                    'message' => 'Sua solicitação de afiliação já foi aceita com sucesso! Você já pode acessar o novo produto.',
                    'data' => []
                ];

            } else {
                return [
                    'error' => true,
                    'message' => 'Falha ao cadastrar afiliado!',
                    'data' => ['Producer nao foi encontrada ou gerada!']
                ];
            }

        } catch (\Exception $e) {
            Log::info("degub_affiliate : erro ao aceitar o convite de afiliacao!!!", [$e]);
            return [
                'error' => true,
                'message' => $e->getMessage(),
                'data' => []
            ];
        }

    }

    public function getProducerOrCreate($data)
    {
        $producer = Producer::where('type', 'A')
            ->where('platform_id', $data['platform_id'])
            ->where('platform_user_id', $data['platform_user_id'])
            ->first();

        if($producer)
            return $producer;

        return Producer::create($data);

    }

}
