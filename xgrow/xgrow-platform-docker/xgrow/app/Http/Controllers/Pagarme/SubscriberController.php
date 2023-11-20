<?php

namespace App\Http\Controllers\Pagarme;

use App\Http\Controllers\Controller;
use App\Mail\SendMailRefund;
use App\Payment;
use App\Services\Contracts\SubscriptionServiceInterface;
use App\Services\EmailService;
use App\Utils\TriggerIntegrationJob;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * @deprecated use \App\Http\Controllers\Api\CheckoutController instead
 */
class SubscriberController extends Controller
{
    use TriggerIntegrationJob;

    private $subscriptionService;

    public function __construct(SubscriptionServiceInterface $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    public function refundPix(Request $request, $platform_id, $payment_id)
    {
        try {
            $reason = $request->input('cancellationReason');
            if ($reason == null) throw new Exception('É obrigatório descrever o motivo do estorno.');
            if (strlen($reason) < 10 || strlen($reason) > 100) throw new Exception('O motivo deve ter entre 10 e 100 caracteres.');

            $payment = Payment::where('id', $payment_id)
                ->where('status', Payment::STATUS_PAID)
                ->firstOrFail();

            $payment->cancellation_reason = $reason;
            $payment->cancellation_at = Carbon::now();
            $payment->cancellation_user = Auth::user()->id;
            $payment->save();

            $refundedTransaction = $this->subscriptionService->refundPix($payment);

            if ($refundedTransaction->status == 'refunded') {
                try {
                    $subscriber = $payment->subscriber;
                    EmailService::mail(
                        [$subscriber->email],
                        new SendMailRefund(
                            $platform_id,
                            $subscriber,
                            $payment,
                            $refundedTransaction->authorization_code ?? '',
                            $payment->price,
                            $payment->getTotalPlansValue()
                        )
                    );
                } catch (Exception $e) {
                    // ignore errors when sending email
                }

                $this->triggerPaymentRefundEvent($payment);
                $this->triggerSubscriptionCanceledEvent($payment);
            }

            return response()->json($refundedTransaction);
        } catch (Exception $e) {
            Log::error('pagarme.refund-pix', [
                'platform_id' => $platform_id,
                'payment_id' => $payment_id,
                'exception_class' => get_class($e),
                'exception_message' => $e->getMessage(),
                'exception_code' => $e->getCode(),
            ]);
        }
    }

    public function refundBoleto($platform_id, $payment_id, Request $request)
    {
        try {
            $payment = Payment::where('id', $payment_id)
                ->where('status', Payment::STATUS_PAID)
                ->firstOrFail();

            $refundedTransaction = $this->subscriptionService->refundBoleto(
                $payment,
                $request->bankCode,
                $request->agency,
                $request->agencyDigit,
                $request->account,
                $request->accountDigit,
                $request->documentNumber,
                $request->legalName
            );

            if ($refundedTransaction->status == 'refunded') {
                try {
                    $subscriber = $payment->subscriber;
                    EmailService::mail(
                        [$subscriber->email],
                        new SendMailRefund(
                            $platform_id,
                            $subscriber,
                            $payment,
                            $refundedTransaction->authorization_code ?? '',
                            $payment->price,
                            $payment->getTotalPlansValue()
                        )
                    );
                } catch (Exception $e) {
                    // ignore errors when sending email
                }

                $this->triggerPaymentRefundEvent($payment);
                $this->triggerSubscriptionCanceledEvent($payment);
            }

            return response()->json($refundedTransaction);
        } catch (Exception $e) {
            Log::error('pagarme.refund-boleto', [
                'platform_id' => $platform_id,
                'payment_id' => $payment_id,
                'exception_class' => get_class($e),
                'exception_message' => $e->getMessage(),
                'exception_code' => $e->getCode(),
            ]);
        }
    }


}
