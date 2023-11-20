<?php

namespace App\Services\Actions;

use App\Client;
use App\Payment;
use App\PaymentPlan;
use App\Platform;
use App\Services\Contracts\SubscriptionServiceInterface;
use App\Services\MundipaggService;
use App\Services\Subscriptions\SubscriptionService;
use App\Utils\TriggerIntegrationJob;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use PagarMe\Client as PagarmeClient;

class PagarmeChargebackUpdate
{

    use TriggerIntegrationJob;
    /** Error Codes
     *
     * TF - Error on foreach transaction
     * CBR - Error on chargeback routine
     * TFSV - Split Value Error
     * CBP - Platform Error
     * PV - Payment Error
     * CSC - Cancel Subscription Erro
     *
     **/

    protected int $timestamp;

    protected string $recipientId;
    public function __construct()
    {
        $this->timestamp = Carbon::now()->subDays(2)->getTimestampMs();
    }

    public function __invoke()
    {
        $pagarme = new PagarmeClient(env('PAGARME_API_KEY'));
        $platforms = Platform::all();

        if (count($platforms) <= 0) {
            return;
        }

        Log::debug('[Chargeback] Processing for all platforms', [
            'timestamp' => $this->timestamp ?? null,
        ]);

        $chargebacks = $pagarme->transactions()->getList([
            'count' => 1000,
            'page' => 1,
            'status' => 'chargedback',
            'date_updated' => ">{$this->timestamp}"
        ]);

        Log::debug('[Chargeback] Total chargebacks found', [
            'chargeback_count' => count($chargebacks) ?? 0,
        ]);

        try {
            foreach ($platforms as $platform) {
                try {
                    $transactions = [];
                    $pid = 0;

                    // Get the MundiPagg Recepient ID
                    $platformId = $platform->id;
                    $client = Client::find($platform->customer_id);

                    Log::withContext(['platform_id' => $platformId ?? null]);
                    Log::withContext(['client_id' => $client->id ?? null]);

                    $mundipaggService = new MundipaggService();
                    $recipientId = $platform->recipient_id ?? $client->recipient_id;

                    if (is_null($recipientId)) {
                        Log::warning('[Chargeback] Recipient Id not found');
                        continue;
                    }

                    Log::withContext(['recipient_id' => $recipientId]);

                    $pagarmeRecipientId = $mundipaggService->convertToPagarMeRecipientId($recipientId);
                    if (!$pagarmeRecipientId) {
                        Log::warning('[Chargeback] Pagar.me Recipient Id not found');
                        continue;
                    }

                    $this->recipientId = $pagarmeRecipientId;

                    Log::withContext(['pagarme_recipient_id' => $pagarmeRecipientId]);

                    $totalChargebacksForPlatform = 0;
                    foreach ($chargebacks as $chargeback) {
                        try {
                            Log::debug('[Chargeback] Processing for platform', [
                                'number' => $totalChargebacksForPlatform ?? null,
                            ]);

                            $totalChargebacksForPlatform++;

                            $orderCode = $chargeback->metadata->order_code ?? null;
                            if (!$orderCode) {
                                continue;
                            }

                            $splitRules = $chargeback->split_rules ?? [];
                            if (count($splitRules) == 2) {
                                $serviceValue = null;
                                $customerValue = null;
                                foreach ($splitRules as $rules) {
                                    if ($rules->recipient_id == $this->recipientId) {
                                        $customerValue = $rules->amount;
                                    } else {
                                        $serviceValue = $rules->amount;
                                    }
                                }
                                if ($serviceValue && $customerValue) {
                                    array_push($transactions, [
                                        'order_code' => $orderCode,
                                        'service_value' => $serviceValue / 100,
                                        'customer_value' => $customerValue / 100,
                                        'split' => 2
                                    ]);
                                } else {
                                    array_push($transactions, [
                                        'order_code' => $orderCode,
                                        'service_value' => null,
                                        'customer_value' => null,
                                        'split' => 1
                                    ]);
                                }
                            } else {
                                array_push($transactions, [
                                    'order_code' => $orderCode,
                                    'service_value' => null,
                                    'customer_value' => null,
                                    'split' => 1
                                ]);
                            }
                        } catch (Exception $exception) {
                            $this->logExceptionError('[Chargeback] Error while processing', $exception, [
                                'chargeback' => json_encode($chargeback),
                            ]);
                        }
                    }

                    Log::debug('[Chargeback] Total transactions found', [
                        'total' => count($transactions ?? []),
                    ]);

                    foreach ($transactions as $transaction) {
                        try {
                            Log::debug('[Chargeback] Update payment', [
                                'order_code' => $transaction['order_code'] ?? null,
                                'transaction_split' => $transaction['split'] ?? null,
                            ]);

                            $payments = Payment::query()->where('order_code', $transaction['order_code'])
                                ->where('status', '!=', Payment::STATUS_CHARGEBACK)
                                ->get();

                            if ($transaction['split'] == 2) {
                                // update values
                                $payments->each(fn(Payment $payment) => $payment->update([
                                    'service_value' => $transaction['service_value'],
                                    'customer_value' => -abs($transaction['service_value']),
                                ]));
                            }

                            // update status on payment and payment_plan
                            $payments->each(function (Payment $payment) {
                                $payment->update([
                                    'status' => Payment::STATUS_CHARGEBACK,
                                ]);

                                $paymentPlans = $payment->plans();
                                $paymentPlans->updateExistingPivot($paymentPlans->allRelatedIds(), [
                                    'status' => PaymentPlan::STATUS_CHARGEBACK,
                                ]);
                            });

                            if ($payments->count() > 0) {
                                $this->cancelSubscription($transaction['order_code']);
                            }
                        } catch (Exception $exception) {
                            $this->logExceptionError('Erro ao executar comando do chargeback (code: PV)', $exception);
                        }
                    }
                } catch (Exception $exception) {
                    $this->logExceptionError('Erro ao executar comando do chargeback (code: CBP)', $exception);
                }
            }
        } catch (Exception $exception) {
            $this->logExceptionError('Erro ao executar comando do chargeback (code: CBR)', $exception);

            $this->failedJob($exception);
        }
    }

    private function logExceptionError(string $message, Exception $exception, array $moreContext = []): void
    {
        Log::error($message, array_merge($moreContext, [
            'exception' => [
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ],
        ]));
    }

    private function failedJob(Exception $exception)
    {
        if (app()->bound('sentry')) {
            app('sentry')->captureException($exception);
        }

        throw $exception;
    }

    public function cancelSubscription($orderCode)
    {
        try {
            if (!$orderCode) {
                return;
            }

            $payment = Payment::where('payments.order_code', $orderCode)->first();

            if (!$payment) {
                return;
            }

            /** @var SubscriptionService $service */
            $service = app()->make(SubscriptionServiceInterface::class);

            $reason = "Cancelado por chargeback";

            $isUnlimited = $payment->type == Payment::TYPE_UNLIMITED;
            if ($isUnlimited) {
                $service->cancelSubscriptionsAndPayments($payment->order_number, $reason);
            } else {
                foreach ($payment->plans as $plan) {
                    $service->cancelSubscription($payment->subscriber, $plan, $reason);
                }
            }

            $this->triggerPaymentChargebackEvent($payment);
            $this->triggerSubscriptionCanceledEvent($payment);
        } catch (Exception $exception) {
            Log::error('Erro ao cancelar subscription pelo chargeback (code: CSC). Subscription: '.json_encode($subscription ?? 'Sem erro').' | System error: '.$exception->getMessage());
        }
    }
}
