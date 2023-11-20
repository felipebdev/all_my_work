<?php

namespace App\Http\Controllers;

use App\PaymentPlan;
use Carbon\Carbon;
use Exception;
use App\Payment;
use App\Mail\SendMailRefund;
use App\Mail\SendMailBankSlip;
use App\Services\EmailService;
use App\Mail\SendMailPaymentConfirmed;
use App\Services\Contracts\PaymentServiceInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class PaymentController extends Controller
{

    private $paymentService;

    public function __construct(PaymentServiceInterface $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function sendBankSlip(Payment $payment)
    {
        try {
            $subscriber = $payment->subscriber;
            EmailService::mail(
                [$subscriber->email],
                new SendMailBankSlip(Auth::user()->platform_id, $subscriber, $payment)
            );

            return response()->json(['status' => 'success', 'message' => 'Boleto reenviado com sucesso!'], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Um erro genérico ocorreu. Tente novamente mais tarde.'], 500);
        }
    }

    public function sendPurchaseProof(Payment $payment)
    {
        try {
            $subscriber = $payment->subscriber;
            EmailService::mail(
                [$subscriber->email],
                new SendMailPaymentConfirmed(Auth::user()->platform_id, $subscriber, $payment)
            );

            return response()->json(['status' => 'success', 'message' => 'Comprovante de confirmação de compra reenviado com sucesso!'], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Um erro genérico ocorreu. Tente novamente mais tarde.'], 500);
        }
    }

    public function sendRefund($paymentPlanId)
    {
        try {
            $paymentPlan = PaymentPlan::find($paymentPlanId);
            $payment = $paymentPlan->payment;
            $subscriber = $payment->subscriber;
            EmailService::mail(
                [$subscriber->email],
                new SendMailRefund(Auth::user()->platform_id, $subscriber, $paymentPlan, $payment->order_code, $paymentPlan->plan_value, null, $payment->updated_at)
            );

            return response()->json(['status' => 'success', 'message' => 'Comprovante de estorno reenviado com sucesso!'], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Um erro genérico ocorreu: ' . $e->getMessage()], 500);
        }
    }

    public function getData($paymentPlanId)
    {
        $paymentPlan = PaymentPlan::find($paymentPlanId);
        $payment = $paymentPlan->payment;
        $subscriber = $payment->subscriber;
        $plan = ($payment->type === 'R') ? [$payment->recurrences[0]->plan] : $paymentPlan->plan;

        $data = [
            'refund' => [
                'total' => $payment->price,
                'code' => $payment->order_code
            ],
            'subscriber' => [
                'name' => $subscriber->name ?? '',
                'email' => $subscriber->email ?? '',
                'document_type' => $subscriber->document_type ?? '',
                'document_number' => $subscriber->document_number ?? '',
                'cellphone' => $subscriber->cel_phone ?? '',
            ],
            'purchase' => [
                'product' => $plan->name,
                'total' => $plan->price
            ]
        ];

        return response()->json($data, 200);
    }

    public function refundCreditCard(Request $request, Payment $payment, $single = "false")
    {
        if ($single == "true") {
            $single = true;
        } else {
            $single = false;
        }

        try {
            $reason = $request->input('cancellationReason');
            if ($reason == null) throw new Exception('É obrigatório descrever o motivo do estorno.');
            if (strlen($reason) < 10 || strlen($reason) > 100) throw new Exception('O motivo deve ter entre 10 e 100 caracteres.');
            $payment->cancellation_reason = $reason;
            $payment->cancellation_at = Carbon::now();
            $payment->cancellation_user = Auth::user()->id;
            $payment->save();
            $this->paymentService->refund($payment, $single);
            return response()->json(['status' => 'success', 'message' => 'Estorno realizado com sucesso!'], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => '' . $e->getMessage()], 500);
        }
    }
}
