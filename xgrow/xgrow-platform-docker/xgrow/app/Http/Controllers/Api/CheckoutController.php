<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\CustomResponseTrait;
use App\Services\Checkout\RefundService;
use App\Services\Checkout\TransferService;
use Exception;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class CheckoutController extends Controller
{
    use CustomResponseTrait;

    private TransferService $transferService;
    private RefundService $refundService;

    public function __construct(TransferService $transferService, RefundService $refundService)
    {
        $this->transferService = $transferService;
        $this->refundService = $refundService;
    }

    public function listTransfers()
    {
        try {
            $response = $this->transferService->listTransfers();

            return $this->customJsonResponse('', 200, ['transfers' => $response]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, ['response' => 'fail']);
        }
    }

    /**
     * Refund values by type
     *
     * @deprecated Use {@see \App\Http\Controllers\Subscriber\SubscriberPaymentsController::refund()}
     * @param  Request  $request
     * @return JsonResponse
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws GuzzleException
     */
    public function refund(Request $request, RefundService $refundService)
    {
        try {
            $type = $request->input('type');
            $paymentPlanId = $request->input('payment_plan_id');
            $reason = $request->input('reason');

            if (!$type) {
                throw new Exception("Tipo de reembolso não informado!");
            }
            if (!$paymentPlanId) {
                throw new Exception("Identificação do pagamento não informada!");
            }
            if ($reason === null) {
                throw new Exception('Motivo do estorno não informado.');
            }
            if (strlen($reason) < 10 || strlen($reason) > 50) {
                throw new Exception('O motivo do estorno deve ter entre 10 e 50 caracteres.');
            }

            $refundService->refundRequest($request);

            return $this->customJsonResponse('Estorno realizado com sucesso. Esta página será atualizada automaticamente.', 200);
        } catch (ClientException $e) {
            $response = $e->getResponse();
            $data = json_decode($response->getBody(), true);

            Log::error('Failed to refund', [
                'response' => $data,
            ]);

            if ($response->getStatusCode() == Response::HTTP_UNPROCESSABLE_ENTITY) {
                // validation error
                $message = collect($data['errors'] ?? [])->flatten()->implode("\n");
                return $this->customJsonResponse($message, Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            return $this->customJsonResponse($data['message'], Response::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400);
        }
    }

}
