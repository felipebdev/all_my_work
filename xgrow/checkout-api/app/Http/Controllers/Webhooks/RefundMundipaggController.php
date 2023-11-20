<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Payment;
use App\PaymentPlan;
use App\Services\Finances\Objects\Constants;
use App\Utils\TriggerIntegrationJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class RefundMundipaggController extends Controller
{

    use TriggerIntegrationJob;

    public function boletoRefunded(Request $request)
    {
        Log::withContext(['request' => $request->all()]);

        $requestType = $request->type;
        Log::withContext(['request_type' => $requestType]);

        if ($requestType !== 'charge.refunded') {
            return $this->success('Somente são processados estornos');
        }

        $valid = [
            Constants::MUNDIPAGG_PAYMENT_METHOD_BOLETO,
        ];

        $paymentMethod = $request->data['payment_method'] ?? null;

        Log::withContext(['payment_method' => $paymentMethod]);

        if (!in_array($paymentMethod, $valid)) {
            Log::error('Wrong payment method');
            return $this->success('Somente são processados pagamentos do tipo boleto bancário');
        }

        $status = $request->data['status'];
        Log::withContext(['status' => $status]);

        if ($status != Constants::MUNDIPAGG_CANCELED) {
            Log::error('Invalid status');
            return $this->success('Somente são processados cancelamentos');
        }

        $chargeId = $request->data['id'] ?? null;
        $payment = Payment::where('charge_id', $chargeId)->first();

        Log::withContext(['charge_id' => $chargeId]);

        if (!$payment) {
            Log::error('Payment not found');
            return $this->fail('Pagamento não encontrado');
        }

        $payment->status = Payment::STATUS_REFUNDED;
        $payment->refund_failed_at = null;
        $payment->save();

        $this->triggerPaymentRefundEvent($payment);

        $paymentPlans = $payment->plans();
        $paymentPlans->updateExistingPivot($paymentPlans->allRelatedIds(), [
            'status' => PaymentPlan::STATUS_REFUNDED,
            'refund_failed_at' => null,
        ]);

        Log::debug('Postback processed successfully');

        return response()->noContent();
    }

    private function success(string $message): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
        ]);
    }

    private function fail(string $message, int $statusCode = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], $statusCode);
    }

}
