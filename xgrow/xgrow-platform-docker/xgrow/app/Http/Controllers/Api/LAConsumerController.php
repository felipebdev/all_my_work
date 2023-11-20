<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\CustomResponseTrait;
use App\Repositories\LA\LaConsumerRepository;
use App\Services\LA\LaConsumerService;
use App\Services\LA\LaMobileConsumerService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LAConsumerController extends Controller
{

    private LaConsumerService $laConsumerService;
    private LaMobileConsumerService $laMobileConsumerService;
    private LaConsumerRepository $laConsumerRepository;

    use CustomResponseTrait;

    public function __construct(LaConsumerService $laConsumerService,
                                LaMobileConsumerService $laMobileConsumerService,
                                LaConsumerRepository $laConsumerRepository)
    {
        $this->laConsumerService = $laConsumerService;
        $this->laMobileConsumerService = $laMobileConsumerService;
        $this->laConsumerRepository = $laConsumerRepository;
    }

    /**
     * Get Subscriber List
     * @param Request $request
     * @return JsonResponse
     */
    public function subscriberList(Request $request): JsonResponse
    {
        try {
            $decoded = $this->laConsumerService->hasToken($request->input('token'));
            $query = $this->laConsumerRepository->getSubscriberList($decoded->platformId, $request->input('subscriberIds'));
            $data = ['subscribers' => $query->get()];

            return $this->customJsonResponse('Lista de alunos carregado.', 200, $data);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    /**
     * Get Course List
     * @param Request $request
     * @return JsonResponse
     */
    public function courseList(Request $request): JsonResponse
    {
        try {
            $decoded = $this->laConsumerService->hasToken($request->input('token'));
            $query = $this->laConsumerRepository->getCourseList($decoded->platformId, $request->input('courseIds'));
            $data = ['courses' => $query->get()];

            return $this->customJsonResponse('Lista de cursos/aulas carregada.', 200, $data);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    /**
     * Update Last Access for Subscribers List
     * @param Request $request
     * @return JsonResponse
     */
    public function updateSubscriberLastAccess(Request $request): JsonResponse
    {
        try {
            $decoded = $this->laConsumerService->hasToken($request->input('token'));
            $this->laConsumerRepository->updateSubscriberLastAccessList($request->input('subscriberIds'));

            return $this->customJsonResponse('Dados atualizados com sucesso.', 204);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    /**
     * Update Expo LA Token for Subscribers
     * @param Request $request
     * @return JsonResponse
     */
    public function updateSubscriberExpoLAToken(Request $request): JsonResponse
    {
        try {
            Log::info('New Expo LA Token request', [
                'request' => $request->all(),
            ]);

            $decoded = $this->laMobileConsumerService->hasToken($request->bearerToken());

            Log::info('New Expo LA Token decoded', [
                'decoded_platform_id' => $decoded->platformId,
                'decoded_user_id' => $decoded->userId,
                'expo_token_begin' => substr($decoded->expoUserIdentificationToken, 0, 20),
            ]);

            $this->laConsumerRepository->updateSubscriberExpoLAToken($decoded->userId,
                                                                     $decoded->expoUserIdentificationToken);

            return $this->customJsonResponse('Dados atualizados com sucesso.', 204);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

}
