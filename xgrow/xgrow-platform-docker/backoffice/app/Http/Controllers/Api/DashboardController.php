<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DashboardIntervalDateRequest;
use App\Http\Traits\CustomResponseTrait;
use App\Services\Dashboard\DashboardService;
use Exception;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{

    use CustomResponseTrait;

    private DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function summary(): JsonResponse
    {
        try {
            $summary = $this->dashboardService->getSummary();

            return $this->customJsonResponse(
                'Dados carregados com sucesso.',
                200,
                [
                    'summary' => $summary
                ]
            );

        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    public function salesSummary(DashboardIntervalDateRequest $request): JsonResponse
    {
        try {
            $saleSummary = $this->dashboardService->getSalesSummary(
                $request->input('date_start'),
                $request->input('date_end')
            );

            return $this->customJsonResponse(
                'Dados carregados com sucesso.',
                200,
                [
                    'sales' => $saleSummary
                ]
            );

        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    public function salesGraph(DashboardIntervalDateRequest $request): JsonResponse
    {
        try {
            $saleGraph = $this->dashboardService->getSalesGraph(
                $request->input('date_start'),
                $request->input('date_end')
            );

            return $this->customJsonResponse(
                'Dados carregados com sucesso.',
                200,
                [
                    'data' => $saleGraph
                ]
            );

        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }
}
