<?php

namespace App\Services\Payments;

use App\Http\Controllers\Mundipagg\SubscriberController;
use App\Payment;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Services\Contracts\PaymentServiceInterface;
use App\Services\Contracts\SubscriptionServiceInterface;
use App\Utils\TriggerIntegrationJob;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentService implements PaymentServiceInterface {

    use TriggerIntegrationJob;

    private $paymentRepository;
    private $subscriptionService;

    public function __construct(
        PaymentRepositoryInterface $paymentRepository,
        SubscriptionServiceInterface $subscriptionService
    ) {
        $this->paymentRepository = $paymentRepository;
        $this->subscriptionService = $subscriptionService;
    }

    public function refund(Payment $payment, bool $single = false) {
        $cancelResult = app(SubscriberController::class)->cancelCharge(
            Auth::user()->platform_id,
            $payment->id,
            new Request(),
            $payment->cancellation_reason,
            $single
        );

        if ($payment->type === Payment::TYPE_UNLIMITED) {
            if ($cancelResult instanceof JsonResponse &&
                $cancelResult->getStatusCode() === 200
            ) {
                $responseData = $cancelResult->getData();
                if ($responseData->status === Payment::STATUS_CANCELED) {
                    if (!empty($payment->order_number)) {
                        $this->paymentRepository->update(
                            ['order_number' => $payment->order_number],
                            ['status' => 'canceled']
                        );
                    }
                    else if (!empty($payment->installments) &&
                        !empty($payment->installment_number)
                    ) {
                        $totalRemainingInstallments = ($payment->installments - $payment->installment_number) + 1;
                        $payments = $this->paymentRepository->getFromId(
                            $payment->id,
                            $totalRemainingInstallments
                        );

                        $ids = [];
                        foreach ($payments as $value) {
                            if ($value->type === Payment::TYPE_UNLIMITED &&
                                $value->subscriber_id === $payment->subscriber_id && // se alunos iguais: mesmo sem limite
                                $value->installment_number > $payment->installment_number && //se nº parcela for maior: mesmo sem limite
                                ($value->plans->diff($payment->plans)->isEmpty()) //se produtos iguais: mesmo sem limite
                            ) {
                                $ids[] = $value->id;
                            }
                        }

                        if (!empty($ids)) {
                            $this->paymentRepository->batchUpdate(
                                $ids,
                                ['status' => 'canceled']
                            );
                        }
                    }
                }
            }
        }

        try {
            if ($cancelResult instanceof JsonResponse &&
                $cancelResult->getStatusCode() === 200
            ) {
                $this->subscriptionService->cancelSubscriptionByPayment($payment);
                $this->triggerPaymentRefundEvent($payment);
                $this->triggerSubscriptionCanceledEvent($payment);
            }
        }
        catch(Exception $e) {
            Log::error("[Refund payment_id $payment->id]", [$e->getMessage()]);
        }
    }
}
