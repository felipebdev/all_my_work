<?php

namespace App\Http\Controllers\Affiliations;

use App\Helpers\CollectionHelper;
use App\Http\Controllers\CoProducersAffiliations\CoProducersAffiliationsController;
use App\Repositories\Affiliations\AffiliationsRepository;
use App\Repositories\Affiliations\Objects\AffiliationFilter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AffiliatesApiController extends CoProducersAffiliationsController
{
    public function __construct(AffiliationsRepository $affiliationsRepository)
    {
        parent::__construct($affiliationsRepository, true);
    }

    /**
     * @deprecated Use {@see listAllAffiliates()} instead
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listActiveAffiliates(Request $request)
    {
        $offset = $request->input('offset') ?? 25;
        $platformId = Auth::user()->platform_id;

        $filter = AffiliationFilter::fromArray($request->all());
        $affiliates = $this->repository->listActiveAffiliatesOnPlatform($platformId, $filter);

        return $this->customJsonResponse('Dados carregados com sucesso.', Response::HTTP_OK, [
            'affiliates' => CollectionHelper::paginate($affiliates, $offset)
        ]);
    }

    public function listAllAffiliates(Request $request)
    {
        $offset = $request->input('offset') ?? 25;
        $platformId = Auth::user()->platform_id;

        $filter = AffiliationFilter::fromArray($request->all());
        $affiliates = $this->repository->listAllAffiliatesOnPlatform($platformId, $filter);

        return $this->customJsonResponse('Dados carregados com sucesso.', Response::HTTP_OK, [
            'affiliates' => CollectionHelper::paginate($affiliates, $offset)
        ]);
    }

    public function affiliateRanking(Request $request)
    {
        return $this->repository->affiliateRanking($request->all());
    }

}
