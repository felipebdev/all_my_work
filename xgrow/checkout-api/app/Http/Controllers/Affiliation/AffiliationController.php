<?php

namespace App\Http\Controllers\Affiliation;

use App\Exceptions\BadConfigurationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Affiliation\AffiliationSettingsUpdateRequest;
use App\Http\Traits\CustomResponseTrait;
use App\Repositories\Affiliation\AffiliationSettingsRepository;
use App\Services\Affiliation\AffiliationService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AffiliationController extends Controller
{
    use CustomResponseTrait;

    private AffiliationSettingsRepository $affiliationSettingsRepository;

    public function __construct(AffiliationSettingsRepository $affiliationSettingsRepository)
    {
        $this->affiliationSettingsRepository = $affiliationSettingsRepository;
    }

    public function settings($platform_id, $plan_id, Request $request, AffiliationService $affiliationService)
    {
        try {
            $affiliationSettings = $affiliationService->getAffiliationSettings($platform_id, $plan_id);

            if (is_null($affiliationSettings)) {
                $this->customAbort('Afiliação desabilitada para o plano', Response::HTTP_NOT_FOUND);
            }

            return $this->customJsonResponse('', Response::HTTP_OK, $affiliationSettings->toArray());
        } catch (BadConfigurationException $e) {
            $this->customAbort('Configuração de afiliação incorreta', Response::HTTP_SERVICE_UNAVAILABLE);
        }
    }


    public function update($platform_id, $plan_id, AffiliationSettingsUpdateRequest $request)
    {
        $settings = $this->affiliationSettingsRepository
            ->setAffiliationSettingsForPlan($plan_id, $request->validated());

        return $this->customJsonResponse('', Response::HTTP_OK, $settings->toArray());
    }
}
