<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\CustomResponseTrait;
use App\Services\EmailService;
use App\Subscriber;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BackofficeController extends Controller
{

    use CustomResponseTrait;

    /**
     * Resend subscriber data from Backoffice
     * @param Request $request
     * Receive form backoffice $subscriberId
     * @param EmailService $emailService
     * @return JsonResponse
     */
    public function resendSubscriberData(Request $request, EmailService $emailService): JsonResponse
    {
        try {
            $subscriber = Subscriber::findOrFail($request->subscriberId);

            if ($subscriber === null) throw new Exception('Assinante não foi encontrado!', 400);

            $res = $emailService->sendMailNewRegisterSubscriber($subscriber);

            if (!$res) throw new Exception('Plano desse assinante não está habilitado para envio de e-mail!', 400);

            return $this->customJsonResponse('Dados enviados com sucesso', 200, ['extra' => $res]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }
}
