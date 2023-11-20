<?php

namespace App\Http\Controllers\Pagarme;

use App\Http\Controllers\Mundipagg\MundipaggCheckoutController;
use App\Payment;
use App\Plan;
use App\Platform;
use App\Services\EmailService;
use App\Services\Mundipagg\SplitService;
use App\Services\MundipaggService;
use App\Subscriber;
use App\Subscription;
use App\Utils\TriggerIntegrationJob;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use MundiAPILib\Models\CreateOrderRequest;
use MundiAPILib\Models\GetChargeResponse;
use MundiAPILib\Models\GetOrderResponse;

class CheckoutOrderService extends \App\Services\Mundipagg\CheckoutOrderService
{
    use TriggerIntegrationJob;

    protected function getPagarMeRecipientId(Platform $platform, $mundipaggRecipientId) {
        $recipientId = null;
        $mundipaggService = new MundipaggService($platform->id);
        $clientRecipient = $mundipaggService->getClient()->getRecipients()->getRecipient($mundipaggRecipientId);

        foreach( $clientRecipient->gatewayRecipients as $cod=>$attr ) {
            if( $attr->gateway == 'pagarme' ) {
                $recipientId = $attr->pgid;
            }
        }
        return $recipientId;
    }

    private function pagarmeToMundipaggOrderResponse($pagarmeOrder, CreateOrderRequest $orderRequest, Plan $plan) {
        $orderResponse = new GetOrderResponse();
        $orderResponse->id = $pagarmeOrder->id;
        $orderResponse->currency = 'BRL';
        $orderResponse->status = $pagarmeOrder->status == 'waiting_payment' ? 'pending' : $pagarmeOrder->status;
        $orderResponse->code = $pagarmeOrder->id;
        $orderResponse->customer = $orderRequest->customer;

        $charge = new GetChargeResponse();
        $charge->id = $pagarmeOrder->id;
        $charge->code = $pagarmeOrder->id;
        $charge->gatewayId = $pagarmeOrder->acquirer_id;
        $charge->amount = $pagarmeOrder->amount;
        $charge->paymentMethod = $pagarmeOrder->payment_method;
        $charge->status = $pagarmeOrder->status == 'waiting_payment' ? 'pending' : $pagarmeOrder->status;
        $charge->createdAt = $pagarmeOrder->date_created;
        $charge->customer = $orderRequest->customer;
        $charge->lastTransaction = new \stdClass();
        $charge->lastTransaction->qrCode = $pagarmeOrder->pix_qr_code;
        $charge->lastTransaction->qrCodeUrl = null;
        $orderResponse->charges = [$charge];

        $orderResponse->items = $this->getItems($plan);
        $orderResponse->closed = true;
        $orderResponse->createdAt = $pagarmeOrder->date_created;
        $orderResponse->metadata = json_decode(json_encode($pagarmeOrder->metadata), true);
        return $orderResponse;
    }

    public function createOrder(CreateOrderRequest $orderRequest, Platform $platform, Plan $plan, Request $request, Subscriber $subscriber) {

        $items = array();
        foreach($this->getItems($plan) as $cod=>$item) {
            $items[$cod]['id'] = (string) $item->code;
            $items[$cod]['title'] = $item->description;
            $items[$cod]['unit_price'] = $item->amount;
            $items[$cod]['quantity'] = (string) $item->quantity;
            $items[$cod]['tangible'] = true;
        }

        $installments = ($request->installmentSelected > 0 ? $request->installmentSelected : 1);

        //Sum items amount
        $amount = $this->getTotalAmount($request, $plan);
        $amountWithInterest = $installments*$plan->getInstallmentValue($amount, $request->installmentSelected);

        $splitService = new SplitService($platform->id);
        $split = $splitService->getPaymentSplit($amountWithInterest, $amount, $installments);

        $order_metadata = $this->getOrderMetadata($plan, $request);
        $payment_metadata = $splitService->getPaymentMetadata();
        $metadata = array_merge($order_metadata,$payment_metadata);

        try {
            $pagarme = new \PagarMe\Client(env('PAGARME_API_KEY'));

            //Create transaction
            $order = $pagarme->transactions()->create([
                'amount' => str_replace('.','',(string) number_format($amountWithInterest, 2, '.', '.')),
                'payment_method' => $request->payment_method,
                'pix_expiration_date' => Carbon::now()->addMinutes(20),
                /*'pix_additional_fields' => [
                    [
                        'name' => 'Produto',
                        'value'=>  '2'
                    ]
                ],*/
                'customer' => [
                    'external_id' => (string) $subscriber->id,
                    'name' => $orderRequest->customer->name,
                    'email' => $orderRequest->customer->email,
                    'type' => $orderRequest->customer->type == 'company' ? 'corporation' : 'individual',
                    'country' => (string) ( isset($orderRequest->customer->address) ? strtolower($orderRequest->customer->address->country) : 'br' ),
                    'documents' => [
                        [
                            'type' => (string) strtolower($subscriber->document_type),
                            'number' => (string) $subscriber->document_number
                        ]
                    ],

                    'phone_numbers' => [
                        '+'.$subscriber->phone_country_code.$subscriber->phone_area_code.$subscriber->phone_number
                    ]
                ],
                'items' => $items,
                'metadata' => $metadata,
                'split_rules' => [
                    [
                        'recipient_id' => $this->getPagarMeRecipientId($platform, $splitService->getClientRecipient()),
                        'liable' => $split[0]->options->liable,
                        'charge_processing_fee' => $split[0]->options->chargeProcessingFee,
                        'amount' => $split[0]->amount,
                        'charge_remainder' => $split[0]->options->chargeRemainderFee
                    ],
                    [
                        'recipient_id' => $this->getPagarMeRecipientId($platform, $splitService->getXgrowRecipient()),
                        'liable' => $split[1]->options->liable,
                        'charge_processing_fee' => $split[1]->options->chargeProcessingFee,
                        'amount' => $split[1]->amount,
                        'charge_remainder' => $split[1]->options->chargeRemainderFee
                    ]
                ],
                'postback_url' => route('pagarme.order.paid')
            ]);

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 400);
        }

        return $this->pagarmeToMundipaggOrderResponse($order, $orderRequest, $plan);
    }

    public function pagarmePostback(Request $request)
    {
        Log::info('Pagarme postback');
        Log::info(json_encode($request->all()));

        $pagarme = new \PagarMe\Client(env('PAGARME_API_KEY'));
        $postbackIsValid = $pagarme->postbacks()->validate($request->getContent(), $request->header('X-Hub-Signature'));

        if (!$postbackIsValid) {
            Log::info('Pagarme: invalid payload');
            return response()->json(['status' => 'error', 'message' => 'Payload not found'], 400);
        }

        if (!in_array($request->transaction['payment_method'], array(MundipaggCheckoutController::PAYMENT_METHOD_BOLETO, MundipaggCheckoutController::PAYMENT_METHOD_PIX))) {
            return response()->json(['status' => 'success', 'message' => 'Somente são processados pagamentos do tipo boleto bancário ou pix'], 200);
        }

        $return = true;
        $payment = Payment::where('charge_id', '=', $request->transaction['tid'])
            ->where('order_code', '=', $request->transaction['tid'])->first();

        if ($payment) {
            //Update payment
            $transactionStatusMapping = [
                'waiting_payment' => Payment::STATUS_PENDING,
                'refunded' => Payment::STATUS_CANCELED,
                'pending_refund' => Payment::STATUS_CANCELED,
                'refused' => Payment::STATUS_FAILED,
            ];
            $status = $transactionStatusMapping[$request->transaction['status']] ?? $request->transaction['status'];

            $payment->status = $status;
            $payment->customer_value = $request->transaction['metadata']['customer_value'] ?? null;
            $payment->service_value = $request->transaction['metadata']['service_value'] ?? null;
            $payment->plans_value = $request->transaction['metadata']['plans_value'] ?? null;
            $payment->tax_value = $request->transaction['metadata']['tax_value'] ?? null;
            $payment->antecipation_value = $request->transaction['metadata']['antecipation_value'] ?? null;
            $payment->save();

            //Confirm subscriber status
            if ($request->transaction['status'] == Payment::STATUS_PAID) {
                $subscriber = Subscriber::findOrFail($payment->subscriber_id);
                $subscriber->status = Subscriber::STATUS_ACTIVE;
                $subscriber->save();

                foreach ($payment->plans as $cod => $plan) {
                    $subscription = Subscription::firstOrNew([
                            'platform_id' => $subscriber->platform->id,
                            'plan_id' => $plan->id,
                            'subscriber_id' => $subscriber->id,
                            'order_number' => $payment->order_number]
                    );
                    $subscription->payment_pendent = null;
                    $subscription->status = Subscription::STATUS_ACTIVE;
                    $subscription->save();
                }

                $this->triggerPaymentApprovedEvent($payment);

                // Send new register mail
                $emailService = new EmailService();
                $return &= $emailService->sendMailPurchaseProofAfterCheckout($subscriber->platform, $subscriber, $payment);
            }
            return response()->json($return);
        }
        return response()->json(['status' => 'error', 'message' => 'Pagamento não encontrado'], 400);
    }
}
