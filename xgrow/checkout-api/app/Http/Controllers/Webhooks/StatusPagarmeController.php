<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LeadService;
use App\Payment;
use App\Recurrence;
use App\Services\EmailService;
use App\Services\Finances\Objects\Constants;
use App\Subscriber;
use App\Subscription;
use App\Utils\TriggerIntegrationJob;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use PagarMe\Client;

use function env;
use function response;

class StatusPagarmeController extends Controller
{
    use TriggerIntegrationJob;

    public static bool $skipPostbackSignatureValidation = false;

    private LeadService $leadService;

    public function __construct(LeadService $leadService)
    {
        $this->leadService = $leadService;
    }

    private function postbackSignatureIsValid(Request $request): bool
    {
        if (self::$skipPostbackSignatureValidation) {
            return true;
        }

        $content = $request->getContent() ?? '';
        $signature = $request->header('X-Hub-Signature') ?? '';

        $pagarme = new Client(env('PAGARME_API_KEY'));
        $postbackIsValid = $pagarme->postbacks()->validate($content, $signature);

        return $postbackIsValid;
    }

    public function transactionStatus(Request $request)
    {
        Log::withContext(['route' => route('pagarme.transaction.status'), 'request' => $request->all()]);

        if (!$this->postbackSignatureIsValid($request)) {
            Log::info('Pagarme: invalid signature');
            return $this->fail('Invalid signature');
        }

        //Update only Pix Transactions
        $valid = [
            //Constants::MUNDIPAGG_PAYMENT_METHOD_BOLETO,
            Constants::MUNDIPAGG_PAYMENT_METHOD_PIX,
        ];

        $paymentMethod = $request->transaction['payment_method'];
        Log::withContext(['payment_method' => $paymentMethod]);

        if (!in_array($paymentMethod, $valid)) {
            return $this->success('Somente são processados pagamentos do tipo PIX');
        }

        $transactionId = $request->transaction['tid'];
        Log::withContext(['transaction_id' => $transactionId]);

        $payment = Payment::where('charge_id', $transactionId)->where('order_code', $transactionId)->first();

        if (!$payment) {
            Log::error('Payment not found');
            return $this->fail('Pagamento não encontrado');
        }

        //Update payment
        $transactionStatusMapping = [
            Constants::PAGARME_TRANSACTION_WAITING_PAYMENT => Payment::STATUS_PENDING,
            Constants::PAGARME_TRANSACTION_REFUNDED => Payment::STATUS_REFUNDED,
            Constants::PAGARME_TRANSACTION_PENDING_REFUND => Payment::STATUS_PENDING_REFUND,
            Constants::PAGARME_TRANSACTION_REFUSED => Payment::STATUS_FAILED,
        ];

        $transactionStatus = $request->transaction['status'];
        $status = $transactionStatusMapping[$transactionStatus] ?? $transactionStatus;

        if ($status == Payment::STATUS_PENDING && $payment->status == Payment::STATUS_PAID) {
            Log::error('Paid Payment cant go back to pending');
            return $this->fail('Pagamento já confirmado não pode voltar para pendente', Response::HTTP_OK);
        }

        $oldStatus = $request->old_status ?? null;

        if ($status == Payment::STATUS_PAID && $oldStatus == 'pending_refund') {
            Log::info('Pending refund Payment cant go back to paid');
            return $this->fail('Pagamento com estorno pendente não pode voltar para pago', Response::HTTP_OK);
        }

        Log::withContext(['transaction_status' => $transactionStatus, 'status' => $status]);

        $payment->status = $status;
        if ($status == Payment::STATUS_PAID) {
            $payment->refund_failed_at = null;
        }
        $payment->save();

        $paymentPlans = $payment->plans();
        $paymentPlans->updateExistingPivot($paymentPlans->allRelatedIds(), [
            'status' => $status,
            'refund_failed_at' => null,
        ]);

        $return = true;
        if ($status == Constants::PAGARME_TRANSACTION_PAID) {
            $payment->cancellation_reason = null;
            $payment->cancellation_at = null;
            $payment->cancellation_user = null;

            $payment->save();

            $payment->recurrences->each(fn(Recurrence $recurrence) => $recurrence->update([
                'last_payment' => Carbon::now(),
            ]));

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
                $subscription->canceled_at = null;
                $subscription->save();
            }

            $this->leadService->leadConfirmed($subscriber->id, $payment->plans->pluck('id')->toArray());

            $this->triggerPaymentApprovedEvent($payment);

            $emailService = new EmailService();
            $return = $emailService->sendMailPurchaseProofAfterCheckout($subscriber->platform, $subscriber, $payment);
        } elseif ($status == Constants::PAGARME_TRANSACTION_REFUNDED) {
            $this->triggerPaymentRefundEvent($payment);
        }

        Log::debug('Postback processed successfully');

        return response()->json($return);
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
