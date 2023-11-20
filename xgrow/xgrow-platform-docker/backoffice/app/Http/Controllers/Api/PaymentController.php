<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\CustomResponseTrait;
use App\Payment;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    use CustomResponseTrait;

    /**
     * list payment status
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        try {
            $payment_status = Payment::listStatus();
            return $this->customJsonResponse(
                'Dados carregados com sucesso.',
                200,
                ['payment_status' => $payment_status]
            );
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }
}
