<?php

namespace App\Http\Controllers\Affiliations;

use App\Http\Controllers\Controller;
use App\Services\AffiliatesEventService;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use App\Plan;
use App\Producer;
use App\Repositories\Payments\PaymentRepository;
use App\Http\Traits\CustomResponseTrait;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class AffiliatesEventsApiController extends Controller
{
    use CustomResponseTrait;

    public function getAffiliatesEventsInformation(Request $request)
    {
        try {
            $platformId = request()->route()->parameters()['platformId'] ?? null;
            $affiliatesEventService = new AffiliatesEventService($platformId, Auth::user()->id);

            $input = $request->except(['userId']);
            $userId = $request->userId;
            if ($userId) {
                $affiliateId = Producer::query()
                    ->where('platform_id', $platformId)
                    ->where('platform_user_id', $userId)
                    ->first()
                    ->id;

                $input['affiliateId'] = $affiliateId;
            }

            $events = $affiliatesEventService->getAffiliatesEvents($input);

            $eventsData = $events->getUnsafeData() ?? [];

            if ($events->isError()) {
                return $this->customJsonResponse($events->getMessage(), 400, $eventsData);
            }

            $data = [
                'events' => $eventsData['events'] ?? [],
                'current_page' => $eventsData['current_page'] ?? 1,
                'per_page' => $eventsData['per_page'] ?? 25,
                'total' => $eventsData['total'] ?? 0,
                'total_pages' => $eventsData['total_pages'] ?? 1,
            ];

            return $this->customJsonResponse('Dados recuperados com sucesso.', 200, $data);

        } catch (ClientException $e) {

            $message = "Erro ao conectar na API de eventos.";
            $code = $e->getCode();

            Log::error('Events information: Error while connecting to events API', [
                'contents' => (string) $e->getResponse()->getBody(),
                'exception_code' => $code,
                'exception_message' => $e->getMessage(),
            ]);

            return $this->customJsonResponse("{$message} (code: {$code})", 503, [], $e);
        } catch (Exception $e) {

            Log::error('Events information: Error retrieving event data', [
                'exception_code' => $e->getCode(),
                'exception_message' => $e->getMessage(),
            ]);

            return $this->customJsonResponse($e->getMessage(), 500, [], $e);
        }
    }

    public function getAffiliateEventsFilters(): \Illuminate\Http\JsonResponse
    {

        try {
            $platformId = request()->route()->parameters()['platformId'] ?? null;
            $affiliatesEventService = new AffiliatesEventService($platformId, Auth::user()->id);
            $plans = $affiliatesEventService->getPlans();
            $data = [
                'plans' => $plans,
                'types' => AffiliatesEventService::TYPE,
            ];

            return $this->customJsonResponse('Dados recuperados com sucesso.', 200, $data);
        } catch (Exception $e) {

            Log::error('Error at get filters on affiliates events', [
                'exception_code' => $e->getCode(),
                'exception_message' => $e->getMessage(),
            ]);

            return $this->customJsonResponse($e->getMessage(), 500, [], $e);
        }
    }

    public function getAffiliatesBuyerInformation(Request $request)
    {
        try {
            $paymentRepository = new PaymentRepository();
            $buyers = $paymentRepository->getSubscriberByOrderNumber($request);
            if ($buyers['error']) {
                return $this->customJsonResponse($buyers['message'], 400, []);
            }

            return $this->customJsonResponse('Dados recuperados com sucesso.', 200, ['buyer' => $buyers['data']]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

}
