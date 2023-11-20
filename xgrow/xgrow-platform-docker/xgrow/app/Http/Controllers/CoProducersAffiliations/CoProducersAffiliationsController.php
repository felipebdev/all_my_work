<?php

/** @noinspection ALL */

namespace App\Http\Controllers\CoProducersAffiliations;

use App\Helpers\CollectionHelper;
use App\Http\Traits\CustomResponseTrait;
use App\Producer;
use App\Services\Checkout\BankAccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateBankDataCoproducersAffiliations;
use App\Http\Requests\ValidateDocumentsRequest;

/**
 *
 */
class CoProducersAffiliationsController extends Controller
{
    use CustomResponseTrait;

    /**
     * @var
     */
    protected $repository;

    /**
     * @var bool
     */
    protected bool $paginate = false;

    private BankAccountService $bankAccountService;

    /**
     * @param $repository
     */
    public function __construct($repository, $paginate = false)
    {
        $this->repository = $repository;

        $this->paginate = $paginate;

        $this->bankAccountService = app()->make(BankAccountService::class);
    }

    /**
     * @return JsonResponse
     */
    public function getPlatformsCoProducersAffiliationsActive(Request $request): JsonResponse
    {
        $offset = $request->input('offset') ?? 25;

        $platforms = $this->repository->getPlatformsCoProducersAffiliationsByStatus()
            ->leftJoin('files', 'platforms.thumb_id', '=', 'files.id')
            ->groupby('platform_id')
            ->distinct()->get();

        $data = $this->paginate === true
            ? CollectionHelper::paginate($platforms, $offset)
            : $platforms;

        return $this->customJsonResponse(
            'Dados carregados com sucesso.',
            200,
            [
                'platforms' => $data
            ]
        );
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function getPlatformsCoProducersAffiliationsPending(Request $request): JsonResponse
    {
        $term = $request->term ?? null;

        $platforms = $this->repository->getPlatformsCoProducersAffiliationsByStatus('pending', $term)
            ->leftJoin('files', 'products.image_id', '=', 'files.id')
            ->get();

        $offset = $request->input('offset') ?? 25;

        $data = CollectionHelper::paginate($platforms, $offset);

        return $this->customJsonResponse(
            'Dados carregados com sucesso.',
            200,
            ['platforms' => $data]
        );
    }

    /**
     * @return JsonResponse
     */
    public function getRegisteredBankInformationCoProducerAffiliations()
    {
        $data = $this->bankAccountService->get(Auth::user()->id);
        $converted = [
            'bank' => $data->bank_code ?? null,
            'branch' => $data->agency ?? null,
            'document' => $data->document_number ?? null,
            'client_bank' => $data->bank_name ?? null,
            'branch_check_digit' => $data->agency_digit ?? null,
            'account' => $data->account ?? null,
            'account_check_digit' => $data->account_digit ?? null,
            'holder_name' => $data->legal_name ?? null,
        ];

        return $this->customJsonResponse(
            'Dados carregados com sucesso.',
            200,
            ['data' => $converted]
        );
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function updateBankData(UpdateBankDataCoproducersAffiliations $request): JsonResponse
    {
        $request['document_type'] = strlen(preg_replace('/[^0-9]/', '', $request->document)) === 14 ? 'CNPJ' : 'CPF';

        try {
            $bankData = $this->repository->updateBankInformation($request);

            if ($bankData['code'] != 200) {

                return $this->customJsonResponse(
                    'Falha ao realizar ação',
                    $bankData['code'],
                    ['errors' => $bankData['data']]
                );
            }

            return $this->customJsonResponse(
                'Dados bancarios atualizados com sucesso. ',
                201,
                ['data' => $bankData['data']]
            );
        } catch (\Exception $e) {

            $message = $e->getMessage() ?? 'Erro desconhecido';

            Log::error('Erro na aplicação ' . $message);

            $statusCode = $e->getCode() != 0 ? $e->getCode() : 400;

            return $this->customJsonResponse(
                'Falha ao realizar ação',
                $statusCode,
                ['errors' => json_decode($message)]
            );
        }
    }

    /**
     * @param  Request  $request
     * @param $idProducerProducts
     * @param $productId
     * @return JsonResponse
     */
    public function updateStatusProducerProducts(Request $request, $idProducerProducts, $productId): JsonResponse
    {
        $request->validate([
            'status' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!in_array($value, ['active', 'canceled', 'pending'])) {
                        $fail('O status ' . $value . ' não é válido!');
                    }
                }
            ]
        ]);

        try {

            $updateStatus = $this->repository->updateStatusProducerProducts($request, $idProducerProducts, $productId);

            if ($updateStatus['code'] != 200) {

                return $this->customJsonResponse(
                    'Falha ao realizar ação',
                    $updateStatus['code'],
                    ['errors' => $updateStatus['data']]
                );
            }

            return $this->customJsonResponse(
                'Status atualizado com sucesso. ',
                200,
                ['data' => $updateStatus['data']]
            );
        } catch (\Exception $e) {

            $message = $e->getMessage() ?? 'Erro desconhecido';

            Log::error('Erro na aplicação ' . $message);

            $statusCode = $e->getCode() != 0 ? $e->getCode() : 400;

            return $this->customJsonResponse(
                'Falha ao realizar ação',
                $statusCode,
                ['errors' => json_decode($message)]
            );
        }
    }


    /**
     * @param  Request  $request
     * @param $idProducerProducts
     * @param $productId
     * @return JsonResponse
     */
    public function updateCommissionProducerProducts(Request $request, int $producerProductId): JsonResponse
    {
        $min_commision =  Producer::MIN_COMMISSION_AFFILIATE;
        $max_commision = Producer::MAX_COMMISSION_AFFILIATE;
        $request->validate([
            'percent' => [
                'required',
                'numeric',
                "between:{$min_commision},{$max_commision}"
            ]
        ]);

        try {

            $updateStatus = $this->repository->updateCommissionProducerProducts($request->input('percent'), $producerProductId);

            if ($updateStatus['code'] != 200) {

                return $this->customJsonResponse(
                    'Falha ao realizar ação',
                    $updateStatus['code'],
                    ['errors' => $updateStatus['data']]
                );
            }

            return $this->customJsonResponse(
                'Status atualizado com sucesso. ',
                200,
                ['data' => $updateStatus['data']]
            );
        } catch (\Exception $e) {

            $message = $e->getMessage() ?? 'Erro desconhecido';

            Log::error('Erro na aplicação ' . $message);

            $statusCode = $e->getCode() != 0 ? $e->getCode() : 400;

            return $this->customJsonResponse(
                'Falha ao realizar ação',
                $statusCode,
                ['errors' => json_decode($message)]
            );
        }
    }

    /**
     * @param  ValidateDocumentsRequest  $request
     * @return JsonResponse
     */
    public function validateDocuments(ValidateDocumentsRequest $request): JsonResponse
    {
        $validateDocuemnt = $this->repository->validateDocuments($request);

        if ($validateDocuemnt['code'] != 200) {

            return $this->customJsonResponse(
                $validateDocuemnt['data'],
                $validateDocuemnt['code'],
                ['errors' => $validateDocuemnt['data']]
            );
        }

        return $this->customJsonResponse(
            $validateDocuemnt['data'],
            200,
            ['data' => $validateDocuemnt['data']]
        );
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function listWithdrawals(Request $request): JsonResponse
    {
        try {
            $filters = [];
            if ($request->dateRange !== null) {
                $dateRange = explode('|', $request->dateRange);

                $filters['created_before'] = strtotime($dateRange[1] . " 23:59:59");
                $filters['created_after'] = strtotime($dateRange[0] . " 00:00:00");
            }

            $data = $this->repository->listWithdrawals($filters);

            $results = collect($data['data']);

            $offset = $request->offset ?? 25;
            $data['data'] = CollectionHelper::paginate($results, $offset);

            return $this->customJsonResponse(
                'Dados carregados com sucesso.',
                200,
                ['data' => $data['data']]
            );
        } catch (\Exception $e) {
            return $this->customJsonResponse('Falha ao realizar ação.', 400, ['errors' => $e->getMessage()]);
        }
    }

    /**
     * @return JsonResponse
     */
    public function balance(): JsonResponse
    {
        $data = $this->repository->balance();

        if ($data['code'] != 200) {
            $data['data'] = [];
        }

        return $this->customJsonResponse(
            'Dados carregados com sucesso.',
            200,
            ['data' => $data['data']]
        );
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function withdrawValue(Request $request): JsonResponse
    {
        $data = $this->repository->withdrawValue($request);

        if ($data['code'] != 200) {

            return $this->customJsonResponse(
                $data['data'],
                $data['code'],
                ['errors' => $data['data']]
            );
        }

        return $this->customJsonResponse(
            'Dados carregados com sucesso.',
            200,
            ['data' => $data['data']]
        );
    }

    /**
     * @param  Request  $request
     * @return mixed
     */
    public function financialReportSales(Request $request)
    {
        return $this->repository->report($request->all());
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function saleDetails(Request $request): JsonResponse
    {
        try {
            $id = $request->input('id_sale');

            $data = $this->repository
                ->queryProducerProducts()
                ->where('payment_plan_split.id', $id)
                ->first();

            if (!$data) {
                return $this->customJsonResponse(
                    'Venda não encontrada.',
                    404,
                    ['errors' => 'data not found']
                );
            }

            return $this->customJsonResponse(
                'Dados carregados com sucesso.',
                200,
                [
                    'data' => [
                        'product' => $data->product_name,
                        'plan' => $data->plan_name,
                        'plan_value' => floatval($data->payment_plan_value),
                        'commission' => floatval($data->commission),
                        'client_name' => $data->client_name
                    ]
                ]
            );
        } catch (\Exception $e) {
            return $this->customJsonResponse(
                'Falha ao realizar ação.',
                400,
                ['errors' => $e->getMessage()]
            );
        }
    }

    public function acceptCoProductionRequest($idProducerProducts, $productId): JsonResponse
    {
        try {

            $data = $this->repository->acceptCoProductionRequest($idProducerProducts, $productId);

            if ($data['error'] ?? false) {
                return $this->customJsonResponse(
                    $data['message'],
                    $data['code'],
                    ['errors' => $data['data']]
                );
            }

            return $this->customJsonResponse(
                'Convite aceito com sucesso. ',
                200,
                ['data' => $data['data']]
            );
        } catch (\Exception $e) {

            $message = $e->getMessage() ?? 'Erro desconhecido';
            Log::error('Erro no aceite do convite de coprodução ' . $message);
            $statusCode = $e->getCode() != 0 ? $e->getCode() : 400;

            return $this->customJsonResponse(
                'Falha ao realizar ação',
                $statusCode,
                ['errors' => json_decode($message)]
            );
        }
    }
}
