<?php

namespace App\Http\Controllers\Affiliation;

use App\Exceptions\BadConfigurationException;
use App\Exceptions\ConflictException;
use App\Exceptions\RecipientFailedException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Affiliation\AffiliateStoreRequest;
use App\Http\Traits\CustomResponseTrait;
use App\Repositories\Affiliation\AffiliateRepository;
use App\Services\Affiliation\AffiliateService;
use App\Services\Affiliation\Objects\AffiliateCreation;
use Illuminate\Http\Response;

class AffiliateController extends Controller
{
    use CustomResponseTrait;

    public function store($platform_id, $plan_id, AffiliateStoreRequest $request, AffiliateService $affiliateService)
    {
        try {
            $affilianteCreation = AffiliateCreation::fromArray($request->all());

            $affiliateProduct = $affiliateService->storeNewAffiliate($platform_id, $plan_id, $affilianteCreation);

            return $this->customJsonResponse('Afiliado criado com sucesso', Response::HTTP_CREATED, [
                'id' => $affiliateProduct->producer_id,
            ]);
        } catch (BadConfigurationException $e) {
            $this->customAbort('Configuração de afiliação incorreta', Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (ConflictException $e) {
            $this->customAbort('Usuário já é coprodutor na plataforma', Response::HTTP_CONFLICT);
        } catch (RecipientFailedException $e) {
            $msg = "Não foi possível criar o recebedor. Negado pela análise automática: Verifique os documentos e dados bancários informados. ({$e->getMessage()}).";
            $this->customAbort($msg, Response::HTTP_SERVICE_UNAVAILABLE);
        }
    }


    public function index($platform_id, $plan_id, AffiliateRepository $affiliateRepository)
    {
        $affiliates = $affiliateRepository->listPlanAffiliates($platform_id, $plan_id);

        return $affiliates;
    }

}
