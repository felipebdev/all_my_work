<?php

namespace App\Http\Controllers\Affiliations;

use App\Exceptions\InvalidOperationException;
use App\Exceptions\NotFoundException;
use App\Http\Traits\CustomResponseTrait;
use App\Services\Affiliate\AffiliationContractService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AffiliationContractController
{
    use CustomResponseTrait;

    private AffiliationContractService $affiliationContractService;

    public function __construct(
        AffiliationContractService $affiliationContractService
    ) {
        $this->affiliationContractService = $affiliationContractService;
    }

    public function cancelAffiliationContract(Request $request, $platformId, $producer_product_id)
    {
        try {
            $userPlatformId = Auth::user()->platform_id;

            $result = $this->affiliationContractService->cancelContract($userPlatformId, $producer_product_id);

            if ($result) {
                return response()->noContent();
            }

            return $this->customJsonResponse('Falha ao cancelar afiliação ao produto', Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (NotFoundException $e) {
            return $this->customJsonResponse('Afiliação ao produto não encontrada na Plataforma', Response::HTTP_NOT_FOUND);
        } catch (InvalidOperationException $e) {
            return $this->customJsonResponse('Afiliação ao produto já cancelada', Response::HTTP_GONE);
        }
    }

    public function blockAffiliationContract(Request $request, $platformId, $producer_product_id)
    {
        try {
            $userPlatformId = Auth::user()->platform_id;

            $result = $this->affiliationContractService->blockByContract($userPlatformId, $producer_product_id);

            if ($result) {
                return response()->noContent();
            }

            return $this->customJsonResponse('Falha ao bloquear afiliação ao produto', Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (NotFoundException $e) {
            return $this->customJsonResponse('Afiliação ao produto não encontrada na Plataforma', Response::HTTP_NOT_FOUND);
        }
    }

    public function unblockAffiliateByContract(Request $request, $platformId, $producer_product_id)
    {
        try {
            $userPlatformId = Auth::user()->platform_id;

            $result = $this->affiliationContractService->unblockByContract($userPlatformId, $producer_product_id);

            if ($result) {
                return response()->noContent();
            }

            return $this->customJsonResponse('Falha ao desbloquear afiliado', Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (NotFoundException $e) {
            return $this->customJsonResponse('Afiliação ao produto não encontrada na Plataforma', Response::HTTP_NOT_FOUND);
        }
    }

}
