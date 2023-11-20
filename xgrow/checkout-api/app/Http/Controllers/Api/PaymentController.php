<?php

namespace App\Http\Controllers\Api;

use App\CreditCard;
use App\Facades\JwtPlatformFacade;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreditCardPaymentRequest;
use App\Http\Requests\Api\ListPaymentsRequest;
use App\Logs\ChargeLog;
use App\Mail\SendMailRecurrencePaymentSuccess;
use App\Payment;
use App\Repositories\Payments\PaymentLogRepository;
use App\Services\Contracts\SubscriptionServiceInterface;
use App\Services\EmailTaggedService;
use App\Services\Finances\Payment\Exceptions\FailedTransaction;
use App\Services\Finances\Payment\Manual\ManualPaymentService;
use App\Services\Finances\Payment\Manual\SubscriberPaymentThrottlingService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use MundiAPILib\APIException;

class PaymentController extends Controller
{
    private SubscriptionServiceInterface $subscriptionService;
    private PaymentLogRepository $paymentLogRepository;
    private SubscriberPaymentThrottlingService $paymentThrottlingService;
    private ManualPaymentService $manualPaymentService;

    public function __construct(
        SubscriptionServiceInterface $subscriptionService,
        PaymentLogRepository $paymentLogRepository,
        SubscriberPaymentThrottlingService $paymentThrottlingService,
        ManualPaymentService $manualPaymentService
    ) {
        $this->subscriptionService = $subscriptionService;
        $this->paymentLogRepository = $paymentLogRepository;
        $this->paymentThrottlingService = $paymentThrottlingService;
        $this->manualPaymentService = $manualPaymentService;
    }

    /**
     * List user's payments
     *
     * Status filter is set using comma separated values in 'status' param.
     * List all user's payments if not supplied.
     *
     * Eg: GET /api/payments?status=pending,canceled
     */
    public function index(ListPaymentsRequest $request)
    {
        try {
            $query = Payment::where('subscriber_id', JwtPlatformFacade::getSubscriber()->id)
                ->where('platform_id', JwtPlatformFacade::getPlatformId())
                ->whereDate('payment_date', '<=', Carbon::now()) //Exclude future payments
                ->with('plans:id,name,type_plan,description,platform_id,installment')
                ->when($request->status, function ($query, $status) {
                    $query->whereIn('status', $status);
                });

            $payments = $query->get();
            foreach ($payments as $payment) {
                $payment->remaining_tries = $this->paymentThrottlingService->remainingTries($payment->id);
                $payment->next_date = $this->paymentThrottlingService->nextDateAllowed($payment->id);
            }

            return response()->json($payments);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
    }

    /**
     * Subscription payment using an specific card
     *
     * Request body (JSON)
     * {
     *      'credit_card_id': 123,
     *      'payment_id': 456,
     * }
     */
    public function recurrenceOrder(CreditCardPaymentRequest $orderRequest) {
        ChargeLog::withContext(['request-trace-id' => (string) Str::uuid()]);
        ChargeLog::withContext(['running_origin' => 'learningarea_subscription']);
        ChargeLog::withContext(['hostname-dispatcher' => gethostname()]);

        $subscriberId = JwtPlatformFacade::getSubscriber()->id;
        $creditCardId = $orderRequest->credit_card_id;
        $paymentId = $orderRequest->payment_id;

        ChargeLog::withContext(['subscriber_id' => $subscriberId]);
        ChargeLog::withContext(['credit_card_id' => $creditCardId]);
        ChargeLog::withContext(['payment_id' => $paymentId]);

        ChargeLog::debug('Trying to find credit card');

        $creditCard = CreditCard::where('subscriber_id', '=', $subscriberId)
            ->where('id', '=', $creditCardId)
            ->firstOrFail();

        ChargeLog::debug('Credit card found');

        $payment = Payment::findOrFail($paymentId);;

        $response = $this->getThrottlingResponse($paymentId);
        if ($response) {
            return $response;
        }

        $this->paymentLogRepository->createSubscriberLog(JwtPlatformFacade::getPlatformId(), $paymentId, $subscriberId);

        try {
            $this->manualPaymentService->chargeFailedRecurrence($payment, $creditCard);

            try {
                $this->subscriptionService->enableSubscriptionByPayment($payment);
                $this->sendRecurrencePaymentProof($payment);
            } catch (Exception $e) {
                ChargeLog::error('Exception on recurrenceOrder (enableSubscriptionByPayment)', [
                    'exception' => $e->getMessage()
                ]);
            }

            return response()->json(null, 204);
        } catch (FailedTransaction $e) {
            return response()->json(['message' => $e->getMessage(), 'failures' => $e->getFailures()], 400);
        } catch (APIException $e) {
            $this->manualPaymentService->paymentFailed($payment);

            ChargeLog::error('Exception on recurrenceOrder (APIException)', [
                'response_body' => $e->getResponseBody()
            ]);

            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * No Limit payment using an specific card
     *
     * Request body (JSON)
     * {
     *      'credit_card_id': 123,
     *      'payment_id': 456,
     * }
     */
    public function unlimitedOrder(CreditCardPaymentRequest $request)
    {
        ChargeLog::withContext(['request-trace-id' => (string) Str::uuid()]);
        ChargeLog::withContext(['running_origin' => 'learningarea_nolimit']);
        ChargeLog::withContext(['hostname-dispatcher' => gethostname()]);

        $platformId = JwtPlatformFacade::getPlatformId();
        $subscriber = JwtPlatformFacade::getSubscriber();
        $subscriberId = $subscriber->id;
        $creditCardId = $request->credit_card_id;
        $paymentId = $request->payment_id;

        ChargeLog::withContext(['subscriber_id' => $subscriberId]);
        ChargeLog::withContext(['credit_card_id' => $creditCardId]);
        ChargeLog::withContext(['payment_id' => $paymentId]);

        ChargeLog::debug('Trying to find credit card');

        $creditCard = CreditCard::where('subscriber_id', '=', $subscriberId)
            ->where('id', '=', $creditCardId)
            ->firstOrFail();

        ChargeLog::debug('Credit card found');

        /** @var Payment $payment */
        $payment = Payment::with('plans')->findOrFail($paymentId);

        $invalid = [
            Payment::STATUS_PAID,
            Payment::STATUS_CANCELED,
            // Payment::STATUS_PENDING,
        ];
        if (in_array($payment->status, $invalid)) {
            ChargeLog::debug('Invalid payment status', ['payment_status' => $payment->status]);

            return response()->json(['message' => 'Pagamento já realizado ou cancelado'], 400);
        }

        $response = $this->getThrottlingResponse($paymentId);
        if ($response) {
            return $response;
        }

        try {
            $this->manualPaymentService->chargeFailedUnlimited($payment, $creditCard);

            $this->paymentLogRepository->createSubscriberLog($platformId, $paymentId, $subscriberId);

            try {
                $this->subscriptionService->enableSubscriptionByPayment($payment);
                $mail = new SendMailRecurrencePaymentSuccess($platformId, $subscriber, $payment);

                EmailTaggedService::mail($platformId, 'LEARNING_AREA', $mail);
            } catch (Exception $e) {
                ChargeLog::error('Exception on unlimitedOrder (enableSubscriptionByPayment)', [
                    'exception' => $e->getMessage()
                ]);
            }

            return response()->json(null, 204);
        } catch (FailedTransaction $e) {
            $this->paymentLogRepository->createSubscriberLog($platformId, $paymentId, $subscriberId);

            return response()->json(['message' => $e->getMessage(), 'failures' => $e->getFailures()], 400);
        } catch (APIException $e) {
            $this->manualPaymentService->paymentFailed($payment);

            ChargeLog::error('Exception on unlimitedOrder (APIException)', [
                'response_body' => $e->getResponseBody()
            ]);

            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Get throttling response if required, null otherwise
     *
     * @param  int  $paymentId
     * @return \Illuminate\Http\JsonResponse|null
     */
    private function getThrottlingResponse(int $paymentId): ?JsonResponse
    {
        if ($this->paymentThrottlingService->canTryManualPaymentNow($paymentId)) {
            return null; // authorized, no response required
        }

        $nextDate = $this->paymentThrottlingService->nextDateAllowed($paymentId);

        if ($nextDate) {
            return response()->json([
                'message' => 'Por favor aguarde, a próxima tentativa poderá ser feita às '.$nextDate->format('d/m/Y H:i:s').'.',
                'failures' => []
            ], Response::HTTP_TOO_MANY_REQUESTS);
        }

        return response()->json([
            'message' => 'Limite de tentativas excedido. Favor entrar em contato com o produtor.',
            'failures' => []
        ], Response::HTTP_GONE);
    }

    private function sendRecurrencePaymentProof(Payment $payment)
    {
        $platformId = $payment->platform->id;
        $subscriber = $payment->subscriber;

        $mail = new SendMailRecurrencePaymentSuccess($platformId, $subscriber, $payment);

        EmailTaggedService::mail($platformId, 'LEARNING_AREA', $mail);
    }

}
