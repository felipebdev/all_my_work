<?php

namespace App\Http\Controllers\Api\Students;

use App\Exceptions\Students\OperationNotAllowedException;
use App\Exceptions\Students\PlanNotFoundException;
use App\Exceptions\Students\RecurrenceNotFoundException;
use App\Exceptions\Students\SubscriptionNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Student\StudentChangePlanRequest;
use App\Http\Traits\CustomResponseTrait;
use App\Services\Students\PlanChangeService\PlanChangeService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;

class StudentsPlanChangeController extends Controller
{

    use CustomResponseTrait;

    private PlanChangeService $planChangeService;

    public function __construct(PlanChangeService $planChangeService)
    {
        $this->planChangeService = $planChangeService;
    }

    public function listChangePlans($product_id)
    {
        try {
            $result = $this->planChangeService->listAvailablePlans($product_id);

            return $this->successJsonResponse($result);
        } catch (ModelNotFoundException $e) {
            $this->customAbort('Plano não encontrado', Response::HTTP_NOT_FOUND);
        }
    }

    public function storePlan(StudentChangePlanRequest $request, $product_id)
    {
        try {
            $result = $this->planChangeService->changeSubscriptionToAnotherPlan(
                $request->subscription_id,
                $product_id,
                $request->new_plan_id
            );

            return $this->successJsonResponse($result);
        } catch (OperationNotAllowedException $e) {
            $this->customAbort('Plano não pode ser selecionado', Response::HTTP_BAD_REQUEST);
        } catch (PlanNotFoundException $e) {
            $this->customAbort('Plano não encontrado', Response::HTTP_NOT_FOUND);
        } catch (RecurrenceNotFoundException $e) {
            $this->customAbort('Recorrência não encontrada', Response::HTTP_NOT_FOUND);
        } catch (SubscriptionNotFoundException $e) {
            $this->customAbort('Assinatura não encontrada', Response::HTTP_NOT_FOUND);
        }
    }
}
