<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LeadService;
use App\Payment;
use App\PaymentPlan;
use App\Recurrence;
use App\Services\EmailService;
use App\Services\Finances\Objects\Constants;
use App\Services\Pagarme\PagarmeSdkV5\PagarmeClient;
use App\Subscriber;
use App\Subscription;
use App\Utils\TriggerIntegrationJob;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use PagarmeCoreApiLib\Exceptions\ErrorException;
use function response;

class PaidMundipaggController extends Controller
{

    use TriggerIntegrationJob;

    private LeadService $leadService;
    private PagarmeClient $pagarmeClient;

    public function __construct(LeadService $leadService, PagarmeClient $pagarmeClient)
    {
        $this->leadService = $leadService;
        $this->pagarmeClient = $pagarmeClient;
    }

    public function boletoPaid(Request $request)
    {
        Log::withContext(['request' => $request->all()]);

        $requestType = $request->type;
        Log::withContext(['request_type' => $requestType]);

        if ($requestType !== 'charge.paid') {
            Log::error('Wrong charge type');
            return $this->success('Somente são processados pagamentos aprovados');
        }

        $validPaymentMethods = [
            Constants::MUNDIPAGG_PAYMENT_METHOD_BOLETO,
            //Constants::MUNDIPAGG_PAYMENT_METHOD_PIX
        ];

        $paymentMethod = $request->data['payment_method'] ?? null;

        Log::withContext(['payment_method' => $paymentMethod]);

        if (!in_array($paymentMethod, $validPaymentMethods)) {
            Log::error('Wrong payment method');
            return $this->success('Somente são processados pagamentos do tipo boleto bancário');
        }

        $chargeId = $request->data['id'] ?? null;
        $orderCode = $request->data['order']['code'] ?? null;
        $payment = Payment::where('charge_id', $chargeId)->where('order_code', $orderCode)->first();

        Log::withContext(['charge_id' => $chargeId, 'order_code' => $orderCode]);

        if (!$payment) {
            Log::error('Payment not found');
            return $this->fail('Payment not found');
        }

        $status = $request->data['status'];
        Log::withContext(['status' => $status]);

        if ($status != Constants::MUNDIPAGG_PAID) {
            Log::error('Invalid status');
            return $this->success('Somente são processados pagamentos');
        }

        Log::debug('Procesing postback');

        $payment->confirmed_at = Carbon::now();
        $payment->status = Payment::STATUS_PAID;
        $payment->save();

        $paymentPlans = $payment->plans();
        $paymentPlans->updateExistingPivot($paymentPlans->allRelatedIds(), [
            'status' => PaymentPlan::STATUS_PAID,
        ]);

        $payment->recurrences->each(function (Recurrence $recurrence) {
            $nextPaymentDate = is_null($recurrence->last_payment) ? Carbon::now() : (new Carbon($recurrence->last_payment))->addDays($recurrence->recurrence);

            return $recurrence->update([
                'last_payment' => $nextPaymentDate,
            ]);
        });

        $isMultipleMeans = $payment->multiple_means;
        $multipleMeansType = strtolower($payment->multiple_means_type);
        if ($isMultipleMeans) {
            if (str_contains($multipleMeansType, 'c')) {
                $creditCardPayments = Payment::where('type_payment', Payment::TYPE_PAYMENT_CREDIT_CARD)
                    ->where('order_code', $payment->order_code)
                    ->where('status', Payment::STATUS_PENDING)
                    ->get();

                $this->captureCreditCard($creditCardPayments);
            }
        }

        $subscriber = Subscriber::findOrFail($payment->subscriber_id);
        $subscriber->status = Subscriber::STATUS_ACTIVE;
        $subscriber->save();

        foreach ($payment->plans as $cod => $plan) {
            $subscription = Subscription::firstOrNew([
                'platform_id' => $subscriber->platform->id,
                'plan_id' => $plan->id,
                'subscriber_id' => $subscriber->id,
                'order_number' => $payment->order_number
            ]);

            $subscription->payment_pendent = null;
            $subscription->status = Subscription::STATUS_ACTIVE;
            $subscription->save();
        }

        $this->leadService->leadConfirmed($subscriber->id, $payment->plans->pluck('id')->toArray());

        $this->triggerPaymentApprovedEvent($payment);

        $emailService = new EmailService();
        $return = $emailService->sendMailPurchaseProofAfterCheckout($subscriber->platform, $subscriber, $payment);

        Log::debug('Postback processed successfully');

        return response()->json($return);
    }

    public function captureCreditCard($creditCardPayments)
    {
        foreach ($creditCardPayments as $payment){
            try {
                $result = $this->pagarmeClient->captureByChargeId($payment->charge_id);
                //CAPTURAR CARTAO E ATUALIZAR PAYMENT
                if ($result->status == 'paid') {
                    $payment->confirmed_at = Carbon::now();
                    $payment->status = Payment::STATUS_PAID;
                    $payment->save();

                    $paymentPlans = $payment->plans();
                    $paymentPlans->updateExistingPivot($paymentPlans->allRelatedIds(), [
                        'status' => PaymentPlan::STATUS_PAID,
                    ]);
                } else {
                    Log::info('Capturando cartao de credito nao pago!', ['status' => $result->status?? null]);
                }
            } catch(ErrorException $e) {
                Log::error('Capturando cartao de credito nao pago!', [ 'message' => $e->getMessage()]);
            }
        }
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
