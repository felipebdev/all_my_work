<?php

namespace App\Console\Commands;

use App\Client;
use App\Payment;
use App\Platform;
use App\Services\Contracts\SubscriptionServiceInterface;
use App\Services\MundipaggService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PagarMe\Client as PagarmeClient;

class chargebackWithoutDate extends Command
{

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

    protected $signature = 'pagarme:chargeback';
    protected $description = 'This command update all payments if has chargeback in Pagarme API.';

    protected $recipientId;

    public function handle()
    {
        $pagarme = new PagarmeClient(config('app.pagarme_key'));
        $platforms = Platform::all();

        $chargebacks = $pagarme->transactions()->getList([
            'count' => 1000,
            'page' => 1,
            'status' => 'chargedback'
        ]);

        try {
            if (count($platforms) > 0) {
                foreach ($platforms as $platform) {
                    try {
                        $transactions = $customerValue = [];
                        $serviceValue = $pid = 0;

                        // Get the MundiPagg Recepient ID
                        $platformId = $platform->id;
                        $client = Client::find($platform->customer_id);

                        $mundipaggService = new MundipaggService();
                        $pagarmeRecipientId = $mundipaggService->convertToPagarMeRecipientId($client->recipient_id);
                        if ($pagarmeRecipientId) {
                            $this->recipientId = $pagarmeRecipientId;

                            foreach ($chargebacks as $chargeback) {
                                try {
                                    if (isset($chargeback->metadata->order_code)) {
                                        if (isset($chargeback->split_rules) && count($chargeback->split_rules) == 2) {
                                            foreach ($chargeback->split_rules as $rules) {
                                                if ($rules->recipient_id == $this->recipientId) {
                                                    $customerValue = $rules->amount;
                                                } else {
                                                    $serviceValue = $rules->amount;
                                                }
                                            }
                                            if (($serviceValue == 0 || $serviceValue == null) || ($customerValue == 0 || $customerValue == null)) {
                                                array_push($transactions, [
                                                    'order_code' => $chargeback->metadata->order_code,
                                                    'service_value' => null,
                                                    'customer_value' => null,
                                                    'split' => 1
                                                ]);
                                            } else {
                                                array_push($transactions, [
                                                    'order_code' => $chargeback->metadata->order_code,
                                                    'service_value' => $serviceValue / 100,
                                                    'customer_value' => $customerValue / 100,
                                                    'split' => 2
                                                ]);
                                            }
                                        } else {
                                            array_push($transactions, [
                                                'order_code' => $chargeback->metadata->order_code,
                                                'service_value' => null,
                                                'customer_value' => null,
                                                'split' => 1
                                            ]);
                                        }
                                    }
                                } catch (\Exception $exception) {
                                    Log::error('Objeto: ' . json_encode($chargeback) . ' | Exception: ' . $exception->getMessage());
                                }
                            }

                            foreach ($transactions as $transaction) {
                                try {
                                    $affected = 0;
                                    if ($transaction['split'] == 2) {
                                        $affected = Payment::where('order_code', $transaction['order_code'])
                                            ->where('status', '!=', Payment::STATUS_CHARGEBACK)
                                            ->update([
                                                'status' => Payment::STATUS_CHARGEBACK,
                                                'service_value' => $transaction['service_value'],
                                                'customer_value' => -abs($transaction['service_value']),
                                            ]);
                                    } else {
                                        $affected = Payment::where('order_code', $transaction['order_code'])
                                            ->where('status', '!=', Payment::STATUS_CHARGEBACK)
                                            ->update([
                                                'status' => Payment::STATUS_CHARGEBACK
                                            ]);
                                    }

                                    if ($affected) {
                                        $this->cancelSubscription($transaction['order_code']);
                                    }
                                } catch (\Exception $exception) {
                                    Log::error('Erro ao executar comando do chargeback (code: PV). System error: ' . $exception->getMessage());
                                }
                            }
                        }
                    } catch (\Exception $exception) {
                        Log::error("Erro ao executar comando do chargeback (code: CBP) | Platform: $platformId | Recipient: $this->recipientId | Payment: $pid | System error: " . $exception->getMessage());
                    }
                }
            }
        } catch (Exception $exception) {
            Log::error('Erro ao executar comando do chargeback (code: CBR). System error: ' . $exception->getMessage());
        }
        return 0;
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

            /** @var \App\Services\Subscriptions\SubscriptionService $service */
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
