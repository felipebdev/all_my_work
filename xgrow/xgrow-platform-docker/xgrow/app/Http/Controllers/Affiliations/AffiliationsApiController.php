<?php

namespace App\Http\Controllers\Affiliations;

use App\Helpers\CollectionHelper;
use App\Http\Controllers\CoProducersAffiliations\CoProducersAffiliationsController;
use App\Http\Requests\ChangeAffiliateStatusRequest;
use App\Http\Requests\WithdrawCreateRequest;
use App\Repositories\Affiliations\AffiliationsRepository;
use App\Repositories\Affiliations\Objects\AffiliationFilter;
use App\Services\Checkout\WithdrawService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class AffiliationsApiController extends CoProducersAffiliationsController
{
    private $affiliationsRepository;
    private WithdrawService $withdrawService;

    /**
     * @param  AffiliationsRepository  $affiliationsRepository
     * @param  WithdrawService  $withdrawService
     */
    public function __construct(AffiliationsRepository $affiliationsRepository, WithdrawService $withdrawService)
    {
        $this->affiliationsRepository = $affiliationsRepository;
        parent::__construct($affiliationsRepository, true);
        $this->withdrawService = $withdrawService;
    }

    public function getPlatformsAffiliations(Request $request): JsonResponse
    {
        $offset = $request->input('offset') ?? 25;

        $platforms = $this->affiliationsRepository->getPlatformsAffiliations()
            ->leftJoin('files', 'platforms.thumb_id', '=', 'files.id')
            ->groupby('platform_id')
            ->distinct()->get();

        $data = $this->paginate === true
            ? CollectionHelper::paginate($platforms, $offset)
            : $platforms;

        return $this->customJsonResponse(
            'Dados carregados com sucesso.',
            201,
            [
                'platforms' => $data
            ]
        );
    }

    public function listProductsAffiliates(Request $request)
    {
        try {
            $data = $this->affiliationsRepository->listProductsAffiliates($request->all());

            return response()->json($data);

        } catch (\Exception $e) {

            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    public function listLinksOfAffiliate(int $product_id){
        try {
            $data = $this->affiliationsRepository->listLinksOfAffiliate($product_id);

            return response()->json($data);

        } catch (\Exception $e) {

            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    public function affiliateFilters(Request $request, $platform_id){
        try {
            $data = $this->affiliationsRepository->affiliateFilters($platform_id);

            return response()->json($data);

        } catch (\Exception $e) {

            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    public function getDetailOfAffiliate($platformId, $producerProductId){
        try {
            $data = $this->affiliationsRepository->getDetailOfAffiliate($platformId, $producerProductId);

            return response()->json($data);

        } catch (\Exception $e) {

            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    public function getUserAffiliateData($platformId, $producerId){
        try {
            $data = $this->affiliationsRepository->getUserAffiliateData($platformId, $producerId);

            return response()->json($data);

        } catch (\Exception $e) {

            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    public function changeAffiliateStatusById(ChangeAffiliateStatusRequest $request, $platformId, $producerProductId){

        try {
            $status =  $request->status;
            $data = $this->affiliationsRepository->changeAffiliateStatusById($platformId, $producerProductId, $status);

            return response()->json([
                'error' => false,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    public function listAllAffiliatesByStatus(Request $request)
    {
        $offset = $request->input('offset') ?? 25;
        $filter = AffiliationFilter::fromArray($request->all());
        $affiliates = $this->affiliationsRepository->listAllAffiliatesByStatus($filter);

        return $this->customJsonResponse('Dados carregados com sucesso.', Response::HTTP_OK, [
            'affiliates' => CollectionHelper::paginate($affiliates, $offset)
        ]);
    }

    public function withdrawCreate(WithdrawCreateRequest $request): JsonResponse
    {
        $request = $request->all();

        $platformId = request()->route()->parameters()['platformId'];

        $res = $this->withdrawService->createTransfer($platformId, 'affiliate', $request);

        Log::info('Resposta da criacao de saque do afiliado', ['resp' => $res]);

        if ($res['code'] != 200) {

            return $this->customJsonResponse(
                $res['data']->message,
                $res['code'],
                $res['data']->response ?? []
            );
        }

        return $this->customJsonResponse(
            'Saque efetuado com sucesso!'
        );
    }
}
