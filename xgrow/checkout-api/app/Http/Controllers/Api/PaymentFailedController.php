<?php

namespace App\Http\Controllers\Api;

use App\Facades\JwtWebFacade;
use App\Http\Controllers\Controller;
use App\Http\Traits\CustomResponseTrait;
use App\Logs\ChargeLog;
use App\Mail\SendMailRecurrencePaymentSuccess;
use App\Payment;
use App\Repositories\Payments\PaymentLogRepository;
use App\Services\Contracts\SubscriptionServiceInterface;
use App\Services\EmailTaggedService;
use App\Services\Finances\Payment\Exceptions\FailedTransaction;
use App\Services\Finances\Payment\Manual\ClientPaymentThrottlingService;
use App\Services\Finances\Payment\Manual\ManualPaymentService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use MundiAPILib\APIException;

class PaymentFailedController extends Controller
{

    use CustomResponseTrait;

    private SubscriptionServiceInterface $subscriptionService;
    private PaymentLogRepository $paymentLogRepository;
    private ClientPaymentThrottlingService $paymentThrottlingService;
    private ManualPaymentService $manualPaymentService;

    public function __construct(
        SubscriptionServiceInterface $subscriptionService,
        PaymentLogRepository $paymentLogRepository,
        ClientPaymentThrottlingService $paymentThrottlingService,
        ManualPaymentService $manualPaymentService
    ) {
        $this->subscriptionService = $subscriptionService;
        $this->paymentLogRepository = $paymentLogRepository;
        $this->paymentThrottlingService = $paymentThrottlingService;
        $this->manualPaymentService = $manualPaymentService;

        $this->manualPaymentService->setIsFromPlatform(true);
    }

    public function update(Request $orderRequest, $payment_id)
    {
        ChargeLog::withContext(['request-trace-id' => (string) Str::uuid()]);
        ChargeLog::withContext(['running_origin' => 'manual_payment']);
        ChargeLog::withContext(['hostname-dispatcher' => gethostname()]);
        ChargeLog::withContext(['payment_id' => $payment_id]);

        $payload = JwtWebFacade::getPayload();
        $platformId = $payload->platform_id;

        /** @var Payment $payment */
        $payment = Payment::with('plans')->findOrFail($payment_id);

        if ($payment->platform_id != $platformId) {
            $this->customAbort('Pagamento não encontrado na Plataforma', Response::HTTP_NOT_FOUND);
        }

        $invalid = [
            Payment::STATUS_PAID,
            Payment::STATUS_CANCELED,
            // Payment::STATUS_PENDING,
        ];

        if (in_array($payment->status, $invalid)) {
            ChargeLog::debug('Invalid payment status', ['payment_status' => $payment->status]);

            $this->customAbort('Pagamento já realizado ou cancelado');
        }

        /** @var \App\Subscriber $subscriber */
        $subscriber = $payment->subscriber;

        ChargeLog::debug('Trying to find credit card');
        $creditCard = $subscriber->creditCard; // subscriber's default credit card

        if (!$creditCard) {
            ChargeLog::error('Credit card not found');
            $this->customAbort('Cartão de crédito não encontrado');
        }

        ChargeLog::debug('Credit card found');

        $this->throwThrottlingResponseIfRequired($payment->id);

        $this->paymentLogRepository->createClientLog($platformId, $payment->id, $payload->user_id);

        $isRecurrence = $payment->type == 'R';

        try {
            if ($isRecurrence) {
                $this->manualPaymentService->chargeFailedRecurrence($payment, $creditCard);
            } else {
                $this->manualPaymentService->chargeFailedUnlimited($payment, $creditCard);
            }

            try {
                $this->subscriptionService->enableSubscriptionByPayment($payment);
                $this->sendRecurrencePaymentProof($payment);
            } catch (Exception $e) {
                ChargeLog::error('Exception on manual charge (enableSubscriptionByPayment)', [
                    'exception' => $e->getMessage()
                ]);
            }

            return $this->customJsonResponse('Cobrança efetuada com sucesso');

        } catch (FailedTransaction $e) {
            $this->customAbort('Falha na transação: '. $e->getMessage(), 400, [
                'remaining_tries' => $this->paymentThrottlingService->remainingTries($payment->id),
                'next_date' => $this->paymentThrottlingService->nextDateAllowed($payment->id),
            ]);
        } catch (APIException $e) {
            $this->manualPaymentService->paymentFailed($payment);

            ChargeLog::error('Exception on manual charge (APIException)', [
                'response_body' => $e->getResponseBody()
            ]);

            $this->customAbort('Falha na comunicação com API: '. $e->getMessage(), 400, [
                'remaining_tries' => $this->paymentThrottlingService->remainingTries($payment->id),
                'next_date' => $this->paymentThrottlingService->nextDateAllowed($payment->id),
            ]);
        }
    }

    /**
     * Throws abort exception if throttling is required
     *
     * @param  int  $paymentId
     * @throws \Exception
     */
    private function throwThrottlingResponseIfRequired(int $paymentId): void
    {
        if ($this->paymentThrottlingService->canTryManualPaymentNow($paymentId)) {
            return; // authorized, no response required
        }

        $nextDate = $this->paymentThrottlingService->nextDateAllowed($paymentId);

        if ($nextDate) {
            $msg = 'Por favor aguarde, a próxima tentativa poderá ser feita às '.$nextDate->format('d/m/Y H:i:s').'.';
            $this->customAbort($msg, Response::HTTP_TOO_MANY_REQUESTS);
        }

        $this->customAbort('Limite de tentativas excedido. Favor entrar em contato com o produtor.', Response::HTTP_GONE);
    }

    private function sendRecurrencePaymentProof(Payment $payment)
    {
        $platformId = $payment->platform->id;
        $subscriber = $payment->subscriber;

        $mail = new SendMailRecurrencePaymentSuccess($platformId, $subscriber, $payment);

        EmailTaggedService::mail($platformId, 'PLATFORM', $mail);
    }

}
