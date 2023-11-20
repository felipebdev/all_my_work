<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;
use App\Http\Requests\Subscriber\SubscriberRefundRequest;
use App\Http\Traits\CustomResponseTrait;
use App\Repositories\Subscribers\SubscriberRepository;
use App\Services\Checkout\RefundService;
use Exception;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
/**
 *
 */
class SubscriberPaymentsController extends Controller
{
    use CustomResponseTrait;

    private SubscriberRepository $subscriberRepository;

    /**
     * @param  SubscriberRepository  $subscriberRepository
     */
    public function __construct(SubscriberRepository $subscriberRepository)
    {
        $this->subscriberRepository = $subscriberRepository;
    }

    /**
     * @param $subscriberId
     * @return JsonResponse
     */
    public function listSubscriberPayments($subscriberId, Request $request): JsonResponse
    {
        try {
            $offset = $request->input('offset') ?? 25;
            return $this->customJsonResponse(
                'Dados carregados com sucesso.',
                Response::HTTP_OK,
                ['payments' => $this->subscriberRepository->listSubscriberPayments($subscriberId, $offset)]
            );
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'response' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Refund values by type
     *
     * @param  \App\Http\Requests\Subscriber\SubscriberRefundRequest  $request
     * @param  \App\Services\Checkout\RefundService  $refundService
     * @return \Illuminate\Http\JsonResponse
     */
    public function refund(SubscriberRefundRequest $request, RefundService $refundService)
    {
        try {
            $refundService->refundRequest($request);

            $message = 'Estorno realizado com sucesso. Esta página será atualizada automaticamente.';
            return $this->customJsonResponse($message);
        } catch (ClientException $e) {
            $data = json_decode($e->getResponse()->getBody(), true);

            Log::error('Failed to refund', [
                'response' => $data,
            ]);

            return $this->customJsonResponse($data['message'], Response::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

}
