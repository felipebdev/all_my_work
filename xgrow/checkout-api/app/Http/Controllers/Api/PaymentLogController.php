<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Finances\Payment\Manual\SubscriberPaymentThrottlingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PaymentLogController extends Controller
{
    private SubscriberPaymentThrottlingService $paymentThrottlingService;


    public function __construct(SubscriberPaymentThrottlingService $paymentThrottlingService)
    {
        $this->paymentThrottlingService = $paymentThrottlingService;
    }


    public function index(Request $request, $payment_id)
    {
        if ($this->paymentThrottlingService->canTryManualPaymentNow($payment_id)) {
            return response()->noContent();
        }

        $remainingTries = $this->paymentThrottlingService->remainingTries($payment_id);

        if ($remainingTries) {
            $nextTime = $this->paymentThrottlingService->nextDateAllowed($payment_id);
            return response()->json([
                'message' => 'Por favor aguarde, a próxima tentativa poderá ser feita às '.$nextTime->format('d/m/Y H:i:s').'.',
                'failures' => [],
                'remaining_tries' => $remainingTries,
                'next_date' => $nextTime,
            ], Response::HTTP_TOO_MANY_REQUESTS);
        }

        return response()->json([
            'message' => 'Limite de tentativas excedido. Favor entrar em contato com o produtor.',
            'failures' => [],
            'remaining_tries' => 0,
            'next_date' => null,
        ], Response::HTTP_GONE);
    }


}
