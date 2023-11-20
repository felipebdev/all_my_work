<?php

/** @noinspection ALL */

namespace App\Repositories\CoProducersAffiliations;

use App\Platform;
use App\Repositories\Banks\Banks;
use App\Services\Checkout\BankAccountService;
use App\Services\Checkout\RecipientsService;
use App\Services\Report\ReportBaseService;
use GuzzleHttp\Exception\GuzzleException as GuzzleExceptionAlias;
use Illuminate\Support\Facades\Auth;
use App\Client;
use App\Helpers\BigBoostHelper;
use App\Http\Controllers\BankDataController;
use App\Producer;
use App\ProducerProduct;
use App\Product;
use App\Services\Checkout\CheckoutBaseService;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 *
 */
abstract class AbstractProducersRepository
{
    /**
     * @return string
     */
    abstract public function getType(): string;

    /**
     * @return string
     */
    abstract public function getActingAs(): string;

    /**
     * @var string|null
     */
    private ?string $platformId = null;

    /**
     * @param  string|null  $platformId
     */
    public function __construct(?string $platformId = null, RecipientsService $recipientsService)
    {
        $this->platformId = $platformId;
        $this->recipientsService = $recipientsService;
    }

    /**
     * @param  string  $status
     * @param  string|null  $term
     * @return mixed
     */
    public function getPlatformsCoProducersAffiliationsByStatus(string $status = 'active', string $term = null)
    {
        $query = Platform::select(
            'platforms.id as platform_id',
            'platforms.name as platform_name',
            'platforms.url as platform_url',
            'platforms.cover as platform_cover',
            'files.filename',
            'producers.id as producer_id',
            'producer_products.status as status',
            'producer_products.id as producer_products_id',
            'producer_products.contract_limit',
            'producer_products.percent',
            'products.name as product_name',
            'producer_products.split_invoice'
        )
            ->join('producers', 'platforms.id', '=', 'producers.platform_id')
            ->join('producer_products', 'producers.id', '=', 'producer_products.producer_id')
            ->join('products', 'products.id', '=', 'producer_products.product_id')
            ->where('producers.type', $this->getType())
            ->where('producers.platform_user_id', Auth::user()->id);
        if ($status == 'pending') {

            $query->where(function (Builder $q) use ($status) {
                return $q
                    ->where('producer_products.status', $status)
                    ->orWhere('producer_products.status', 'recipient_failed');
            });
        } else {
            $query->where('producer_products.status', $status);
        }
        if ($term) {

            $query->where(function (Builder $q) use ($term) {
                return $q
                    ->where('products.name', 'like', "%$term%")
                    ->orWhere('platforms.name', 'like', "%$term%");
            });
        }

        return $query;
    }

    /**
     * @deprecated Replaced by {@see BankAccountService::get()}
     *
     * @return array|string[]
     */
    public function getRegisteredBankInformationCoProducerAffiliations(): array
    {
        $platformId = $this->platformId ?? request()->route()->parameters()['platformId'];

        $producer = Producer::where('platform_user_id', Auth::user()->id)
            ->where('type', $this->getType())
            ->where('platform_id', $platformId)
            ->whereNotNull('bank')
            ->whereNotNull('branch')
            ->whereNotNull('account')
            ->whereNotNull('account_check_digit')
            ->where('document_verified', 1)
            ->latest()
            ->first();

        if ($producer) {
            return [
                'bank' => $producer->bank ? Banks::getBankNameByCode($producer->bank) : null,
                'branch' => $producer->branch ?? null,
                'document' => $producer->document ?? null,
                'client_bank' => $producer->bank ?? null,
                'branch_check_digit' => $producer->branch_check_digit ?? null,
                'account' => $producer->account ?? null,
                'account_check_digit' => $producer->account_check_digit ?? null,
                'holder_name' => $producer->holder_name ?? null,
            ];
        }

        return [
            'bank' => null,
            'branch' => null,
            'document' => null,
            'client_bank' => null,
            'branch_check_digit' => null,
            'account' => null,
            'account_check_digit' => null,
            'holder_name' => null,
        ];
    }

    /**
     * @param  Request  $request
     * @return array
     */
    public function updateBankInformation(Request $request): array
    {
        $platformId = $this->platformId ?? request()->route()->parameters()['platformId'];

        $producer = Producer::select('producers.*')
            ->join('producer_products', 'producers.id', '=', 'producer_products.producer_id')
            ->join('products', 'producer_products.product_id', '=', 'products.id')
            ->where('producer_products.status', '<>', 'canceled')
            ->where('products.platform_id', '=', $platformId)
            ->where('platform_user_id', Auth::user()->id)
            ->where('producers.type', $this->getType())
            ->first();

        if (!$producer) {
            return ['data' => 'Co-produtor não encontrado', 'code' => 404];
        }

        $producer->bank = $request->bank;
        $producer->branch = $request->branch;
        $producer->branch_check_digit = $request->branch_check_digit;
        $producer->account = $request->account;
        $producer->account_check_digit = $request->account_check_digit;
        $producer->holder_name = $request->holder_name;
        $producer->document = $request->document;
        $producer->account_type = $request->account_type;
        $producer->document_type = $request->document_type;
        $producer->accepted_terms = true;
        $producer->save();

        return ['data' => 'Dados atualizados com sucesso', 'code' => 200];
    }

    /**
     * @param float $percent
     * @param $producerProductId
     * @return array
     */
    public function updateCommissionProducerProducts(float $percent, int $producerProductId): array
    {
        $producerProduct = ProducerProduct::find($producerProductId);
        if (!$producerProduct) {
            return ['data' => 'Produto não encontrado, ou não pertence ao co-produtor', 'code' => 404];
        }

        if ($producerProduct->percent != $percent) {
            $log = [
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'platform_user_id' => Auth::user()->id,
                'platform_id' => Auth::user()->platform_id,
                'producer_product_id' => $producerProduct->id,
                'producer_id' => (int) $producerProduct->producer_id,
                'product_id' => (int) $producerProduct->product_id,
                'previous_percent' => $producerProduct->percent,
                'current_percent' => $percent
            ];
            $producerProduct->percent = $percent;
            $producerProduct->save();
            Log::info('Afiliatte comission changed', $log);
        }

        return ['data' => 'Comissão atualizada com sucesso', 'code' => 200];
    }

    /**
     * @param  Request  $request
     * @param $id
     * @param $producerId
     * @return array
     */
    public function updateStatusProducerProducts(Request $request, $id, $producerId): array
    {
        $producerProducts = ProducerProduct::where('id', $id)->where('producer_id', $producerId)->first();

        $product = Product::find($producerProducts->product_id);

        if (!$producerProducts) {
            return ['data' => 'Produto não encontrado, ou não pertence ao co-produtor', 'code' => 404];
        }

        $producerProducts->status = $request->status;

        if ($request->status === 'canceled') {

            $producerProducts->canceled_at = Carbon::now();

            $producerProducts->save();

            return ['data' => 'Convite não aceito', 'code' => 200];
        }

        $producerProducts->save();

        $producer = Producer::where('platform_user_id', Auth::user()->id)
            ->where('platform_id', $product->platform_id)
            ->whereNotNull('bank')
            ->whereNotNull('branch')
            ->whereNotNull('account')
            ->whereNotNull('branch_check_digit')
            ->whereNotNull('account_check_digit')
            ->where('document_verified', 1)
            ->where('producers.type', $this->getType())
            ->first();

        if ($producer) {
            Producer::where('platform_user_id', Auth::user()->id)
                ->where('platform_id', $product->platform_id)
                ->where('producers.type', $this->getType())
                ->whereNull('bank')
                ->whereNull('branch')
                ->whereNull('account')
                ->whereNull('branch_check_digit')
                ->whereNull('account_check_digit')
                ->update([
                    'document_type' => $producer->document_type,
                    'document' => $producer->document,
                    'holder_name' => $producer->holder_name,
                    'account_type' => $producer->account_type,
                    'bank' => $producer->bank,
                    'branch' => $producer->branch,
                    'account' => $producer->account,
                    'branch_check_digit' => $producer->branch_check_digit,
                    'account_check_digit' => $producer->account_check_digit,
                    'recipient_id' => $producer->recipient_id,
                    'document_verified' => $producer->document_verified,
                    'recipient_gateway' => $producer->recipient_gateway
                ]);
        }

        return ['data' => 'Convite aceito com sucesso', 'code' => 200];
    }

    /**
     * @param  Request  $request
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function validateDocuments(Request $request): array
    {
        try {

            $platformId = $this->platformId ?? request()->route()->parameters()['platformId'];

            $image = $_FILES['file'];

            $extension = pathinfo($image['name'], PATHINFO_EXTENSION);

            $image['name'] = Uuid::uuid4() . '.' . $extension;

            $producer = Producer::where('platform_user_id', Auth::user()->id)->where(
                'producers.type',
                $this->getType()
            )->where('platform_id', $platformId)->latest()
                ->first();

            if (!$producer) {

                return ['data' => 'Usuário possui cadastro incompleto!', 'code' => 412];
            }

            $bigBoostHelper = new BigBoostHelper;

            $s3 = Storage::disk('documents');

            $bigIDResult = $bigBoostHelper->ocrDocument($image, $producer->document);

            if (array_key_exists('validate_error', $bigIDResult) && $bigIDResult['validate_error'] == true) {

                return ['data' => $bigIDResult['message'], 'code' => $bigIDResult['code']];
            }

            $nameArray = explode(' ', Auth::user()->name);

            $uploadDirectory = strtolower(removeAccentsAndEspecialChars($nameArray[0]) . '-' . removeAccentsAndEspecialChars(end($nameArray)));

            $producer->document_verified = 1;

            $imageContent = file_get_contents($image['tmp_name']);

            $s3->put($image['name'], $imageContent, $uploadDirectory);

            $recipiendId = Producer::where('platform_user_id', Auth::user()->id)
                ->where('producers.type', $this->getType())
                ->whereNotNull('recipient_id')
                ->get()
                ->first();

            if (!$recipiendId) {

                $createRecipient = $this->createRecipient();

                if (!$createRecipient) {

                    return ['data' => 'Não foi possivel criar o recebedor', 'code' => 422];
                }
            } else {

                $producer->recipient_id = $recipiendId->recipient_id;
            }

            $producer->save();

            return ['data' => 'Documento validado com sucesso!', 'code' => 200];
        } catch (Exception $e) {

            $message = $e->getMessage() ?? 'Erro desconhecido';

            Log::error('Erro na aplicação ' . $message);

            $statusCode = $e->getCode() != 0 ? $e->getCode() : 400;

            return ['data' => $message, 'code' => $statusCode];
        }
    }

    /**
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createRecipient()
    {
        try {

            $res = $this->checkoutBaseService()->post('recipients');

            Log::info('Recebedor criado com sucesso', [$res->getBody()]);

            return true;
        } catch (ClientException $e) {

            Log::error('Não foi possível criar recebedor', [$e]);

            return false;
        } catch (ConnectException $e) {

            Log::error('Não foi possível conectar ao servidor de destino', [$e]);

            return false;
        }
    }

    /**
     * @param $filters
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function listWithdrawals($filters = [])
    {
        try {
            $filters['count'] = 1000;
            $res = $this->checkoutBaseService()->get('transfers', ['query' => $filters]);
            return ['data' => json_decode($res->getBody()), 'code' => 200];
        } catch (ClientException $e) {
            return ['data' => json_decode($e->getResponse()->getBody()->getContents())->message, 'code' => 400];
        }
    }

    /**
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function balance()
    {
        $producer = Producer::where('platform_id', request()->route()->parameters()['platformId'] ?? null)
            ->where('platform_user_id', Auth::user()->id)
            ->first();
        try {
            $res = $this->checkoutBaseService()->get('balance', ['verify' => false]);

            return ['data' => json_decode($res->getBody()), 'code' => 200];
        } catch (ClientException $e) {
            return ['data' => json_decode($e->getResponse()->getBody()->getContents())->message, 'code' => 400];
        }
    }

    /**
     * @param  Request  $request
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function withdrawValue(Request $request)
    {
        try {

            $res = $this->checkoutBaseService()->post('transfers', ['json' => $request->all()]);

            return ['data' => json_decode($res->getBody()), 'code' => 200];
        } catch (ClientException $e) {

            return ['data' => json_decode($e->getResponse()->getBody()->getContents())->message, 'code' => 400];
        }
    }

    /**
     * @return \GuzzleHttp\Client
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function checkoutBaseService()
    {
        $checkoutBaseService = new CheckoutBaseService;

        return $checkoutBaseService->connectionConfig(
            $this->platformId ?? request()->route()->parameters()['platformId'],
            Auth::user()->id,
            ['acting_as' => $this->getActingAs()]
        );
    }

    /**
     * @param $productType
     * @param $paymentStatus
     * @param $paymentMethod
     * @param $period
     * @return mixed
     */
    public function queryProducerProducts(
        $productType = null,
        $paymentStatus = null,
        $paymentMethod = null,
        $period = null
    ) {
        return ProducerProduct::select(
            'payment_plan_split.id',
            'payment_plan_split.order_code',
            'payment_plan_split.value as commission',
            'payment_plan_split.type as payment_plan_split_type',
            'payment_plan.plan_value as payment_plan_value',
            'products.name as product_name',
            'payments.price as payment_price',
            'payments.status as payment_status',
            'payments.installments as installments',
            'payments.type_payment as payment_method',
            'payments.created_at as payment_date',
            'subscribers.name as client_name',
            'subscribers.email as client_email',
            'plans.name as plan_name',
            'plans.price as plan_price',
            'plans.type_plan as type_plan'
        )
            ->join('producers', 'producers.id', '=', 'producer_products.producer_id')
            ->join('products', 'producer_products.product_id', '=', 'products.id')
            ->join('payment_plan_split', 'producer_products.id', '=', 'payment_plan_split.producer_product_id')
            ->join('payment_plan', 'payment_plan_split.payment_plan_id', '=', 'payment_plan.id')
            ->join('payments', 'payment_plan.payment_id', '=', 'payments.id')
            ->join('subscribers', 'payments.subscriber_id', '=', 'subscribers.id')
            ->join('plans', 'products.id', '=', 'plans.product_id')
            ->where('producers.platform_id', request()->route()->parameters()['platformId'])
            ->where('producers.platform_user_id', Auth::user()->id)
            ->where('producers.type', $this->getType())
            ->when($productType, function ($query, $productType) {
                $query->whereIn('plans.type_plan', $productType);
            })
            ->when($paymentStatus, function ($query, $paymentStatus) {
                $query->whereIn('payments.status', $paymentStatus);
            })
            ->when($paymentMethod, function ($query, $paymentMethod) {
                $query->whereIn('payments.type_payment', $paymentMethod);
            })
            ->when($period, function ($query, $period) {
                $query->whereBetween('payments.payment_date', $period);
            })
            ->orderBy('payment_plan_split.id', 'DESC')
            ->groupby(
                'payment_plan_split.order_code',
                'payment_plan_split.id',
                'plans.name',
                'plans.price',
                'plans.type_plan'
            )
            ->distinct();
    }

    /**
     * @param  string  $url
     * @param  array  $request
     * @return mixed|null
     * @throws GuzzleExceptionAlias
     */
    public function callFinancialApi(string $url, array $request)
    {
        $queryString = http_build_query($request);

        $url = strlen($queryString) == 0
            ? $url
            : "$url?$queryString";

        $platformId = request()->route()->parameters()['platformId'] ?? Auth::user()->platform_id;

        $req = (new ReportBaseService())->connectionConfig($platformId, Auth::user()->id);

        $res = $req->get($url);

        return json_decode($res->getBody(), true) ?? null;
    }

    public function acceptCoProductionRequest($idProducerProducts, $producerId): array
    {
        $producerProducts = ProducerProduct::where('id', $idProducerProducts)
            ->where('producer_id', $producerId)->first();

        if (!$producerProducts) {
            return [
                'error' => true,
                'message' => 'Produto não encontrado, ou não pertence ao co-produtor!',
                'data' => 'Produto não encontrado, ou não pertence ao co-produtor!',
                'code' => 404
            ];
        }

        $producer = Producer::where('id', $producerId)
            ->where('platform_user_id', Auth::user()->id)
            ->first();

        if ($producer) {

            // Get information from Client
            $client = Client::query()->where('email', Auth::user()->email)->first();

            if ($client) {
                $isCpf = Str::upper($client->type_person) == 'F';

                $producer->document_type = $isCpf ? 'cpf' : 'cnpj';
                $producer->document = $isCpf ? $client->cpf : $client->cnpj;
                $producer->document_verified = $client->verified ?? false;
                $producer->save();
            }

            if (!$producer->document_verified){
                return [
                    'error' => true,
                    'message' => 'Para aceitar o convite a documenteção deve estar verificada!',
                    'data' => ['Para aceitar o convite a documenteção deve estar verificada!'],
                    'code' => 401
                ];
            }

            $recipientExists = is_null($producer->recipient_id) ? false : true;

            if (!$recipientExists) {
                $recipient = $this->recipientsService->createProducerRecipient($producer->platform_id, 'producer');

                $hasCreationError = $recipient['error'] ?? false;

                $recipientExists = !$hasCreationError;

                Log::info("Producer ID: ".$producer->id." Recebedor gerado: ".$recipientExists." Recebedor existente: ".$producer->recipient_id);
            }

            // update "contract"
            $producerProducts->status = $recipientExists ? 'active' : 'recipient_failed';
            $producerProducts->save();

            Log::info("Producer product atualizado com sucesso status: ".$producerProducts->status);
            if (!$recipientExists) {
                return [
                    'error' => true,
                    'message' => 'Falha ao aceitar o convite de coprodução! O recebedor não foi criado!',
                    'data' => ['Falha ao criar recipient_id!'],
                    'code' => 401,
                ];
            }
        } else {
            return [
                'error' => true,
                'message' => 'Plataforma não pertence ao usuário logado!',
                'data' => ['Plataforma não pertence ao usuário logado!'],
                'code' => 401
            ];
        }

        return ['error' => false, 'data' => 'Convite aceito com sucesso', 'code' => 200];
    }
}
