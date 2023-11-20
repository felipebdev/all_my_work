<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;
use App\Repositories\SubscriberProducts\SubscriptionProductsRepository;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 *
 */
class SubscriptionProductsController extends Controller
{
    /**
     * @var SubscriptionProductsRepository
     */
    private SubscriptionProductsRepository $subscriberProductsRepository;

    /**
     * @param SubscriptionProductsRepository $subscriberProductsRepository
     */
    public function __construct(SubscriptionProductsRepository $subscriberProductsRepository)
    {
        $this->subscriberProductsRepository = $subscriberProductsRepository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function changeSubscriptionStatus(Request $request): JsonResponse
    {
        $response = $this->subscriberProductsRepository->changeSubscriptionStatus(
            $request->input('sub_status'),
            $request->input('sub_id'),
            Auth::user()->platform_id
        );

        return response()->json(
            [
                'error' => $response['error'],
                'message' => $response['message'],
                'response' => $response['response'],
            ],
            $response['status_code']
        );
    }

    /**
     * @param int $subscriptionId
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function cancelNotRefund(int $subscriptionId): JsonResponse
    {
        $response = $this->subscriberProductsRepository->cancelNotRefund(
            $subscriptionId,
            Carbon::now(),
            Auth::user()
        );

        return response()->json(
            [
                'error' => $response['error'],
                'message' => $response['message'],
                'response' => $response['response'],
            ],
            $response['status_code']
        );
    }

    /**
     * @param int $paymentId
     * @return JsonResponse
     */
    public function sendPurchaseProof(int $paymentId): JsonResponse
    {
        $response = $this->subscriberProductsRepository->sendPurchaseProof(Auth::user()->platform_id, $paymentId);

        return response()->json(
            [
                'error' => $response['error'],
                'message' => $response['message'],
                'response' => $response['response'],
            ],
            $response['status_code']
        );
    }

    /**
     * @param int $paymentId
     * @return JsonResponse
     */
    public function sendBankSlip(int $paymentId): JsonResponse
    {
        $response = $this->subscriberProductsRepository->sendBankSlip(Auth::user()->platform_id, $paymentId);

        return response()->json(
            [
                'error' => $response['error'],
                'message' => $response['message'],
                'response' => $response['response'],
            ],
            $response['status_code']
        );
    }

    /**
     * @param int $paymentPlanId
     * @return JsonResponse
     */
    public function sendRefund(int $paymentPlanId): JsonResponse
    {
        $response = $this->subscriberProductsRepository->sendRefund(Auth::user()->platform_id, $paymentPlanId);

        return response()->json(
            [
                'error' => $response['error'],
                'message' => $response['message'],
                'response' => $response['response'],
            ],
            $response['status_code']
        );
    }

    /**
     * @param int $paymentPlanId
     * @return Response
     */
    public function refundProof(int $paymentPlanId)
    {
        $data = $this->subscriberProductsRepository->refundProof($paymentPlanId);

        $pdf = PDF::loadView('pdf.refund-proof', compact('data'));

        return $pdf->download('comprovante_de_estorno.pdf');
    }

    /**
     * @param int $subscriberId
     * @return JsonResponse
     */
    public function listProductsBySubscriber(int $subscriberId, Request $request): JsonResponse
    {
        $productsId = $request->input('products_id') ?? [];
        $offset = $request->input('offset') ?? 15;
        $response = $this->subscriberProductsRepository->listProductsBySubscriber($subscriberId, Auth::user()->platform_id, $productsId, $offset);

        return response()->json(
            [
                'error' => $response['error'],
                'message' => $response['message'],
                'response' => $response['response'],
            ],
            $response['status_code']
        );
    }

    /**
     * @param int $subscriberId
     * @return JsonResponse
     */
    public function listPlansBySubscriber(int $subscriberId, Request $request): JsonResponse
    {
        $plansId = $request->input('plans_id') ?? [];
        $response = $this->subscriberProductsRepository->listPlansBySubscriber($subscriberId, Auth::user()->platform_id, $plansId);

        return response()->json(
            [
                'error' => $response['error'],
                'message' => $response['message'],
                'response' => $response['response'],
            ],
            $response['status_code']
        );
    }
}
