<?php

namespace App\Http\Controllers\Api;

use App\CreditCard;
use App\Facades\JwtPlatformFacade;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreditCardPaymentRequest;
use App\Http\Requests\Api\ListPaymentsRequest;
use App\Payment;
use App\Services\Contracts\SubscriptionServiceInterface;
use App\Services\Mundipagg\RecurrenceOrderService;
use App\Services\Mundipagg\SplitService;
use App\Services\MundipaggService;
use App\Subscription;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use MundiAPILib\APIException;
use MundiAPILib\Models\CreateCreditCardPaymentRequest;
use MundiAPILib\Models\CreateOrderItemRequest;
use MundiAPILib\Models\CreateOrderRequest;
use MundiAPILib\Models\CreatePaymentRequest;

class PaymentController extends Controller
{
    private $subscriptionService;

    public function __construct(SubscriptionServiceInterface $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
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
            $subscriber_id = JwtPlatformFacade::getSubscriber()->id;
            $platform_id = JwtPlatformFacade::getPlatformId();

            $query = Payment::where('subscriber_id', $subscriber_id)
                ->where('platform_id', $platform_id)
                ->whereDate('payment_date', '<=', Carbon::now()) //Exclude future payments
                ->with('plans:id,name,type_plan,description,platform_id,installment')
                ->when($request->status, function ($query, $status) {
                    $query->whereIn('status', $status);
                });
            $return = $query->get();
            foreach($return as $cod=>$item) {
                if( empty($item->order_number) ) {
                    $subscriptions = Subscription::where('subscriber_id', $subscriber_id)->where('platform_id', $platform_id)->whereNull('order_number')->get();
                }
                else
                {
                    $subscriptions = Subscription::where('subscriber_id', $subscriber_id)->where('platform_id', $platform_id)->where('order_number', $item->order_number)->get();
                }
                $return[$cod]->subscriptions = $subscriptions;
            }
            return response()->json($return);
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
    public function recurrenceOrder(CreditCardPaymentRequest $request, RecurrenceOrderService $recurrenceOrderService)
    {
        $creditCardId = $request->credit_card_id;
        $paymentId = $request->payment_id;

        $creditCard = CreditCard::where('subscriber_id', '=', JwtPlatformFacade::getSubscriber()->id)
            ->where('id', '=', $creditCardId)
            ->firstOrFail();

        $payment = Payment::findOrFail($paymentId);
        $recurrence = $payment->recurrences->first();
        $subscriber = $recurrence->subscriber;
        $platform = $subscriber->platform;

        try {
            $request = new CreateOrderRequest();
            $request->customerId = $recurrence->subscriber->customer_id;
            $parcel_number = $recurrence->current_charge + 1;
            $request->items = $recurrenceOrderService->getItems($recurrence->plan, $parcel_number);
            $request->metadata = $recurrenceOrderService->getOrderMetadata($recurrence->plan);
            $request->payments = [$recurrenceOrderService->getPaymentRecurence($platform, $recurrence->plan, $creditCard->card_id, $parcel_number)];

            $mundipaggService = new MundipaggService($platform->id);
            $result = $mundipaggService->getClient()->getOrders()->createOrder($request);

            if ($result->status !== 'paid') {
                $this->paymentFailed($payment, $payment->plans);
                return response()->json('Não foi possível efetuar a operação', 400);
            }

            $payment->gateway = 'mundipagg';
            $payment->status = $result->status;
            $payment->order_id = $result->id;
            $payment->customer_id = $result->customer->id;
            $payment->order_code = $result->code;
            $payment->status = Payment::STATUS_PAID;
            $payment->payment_source = Payment::PAYMENT_SOURCE_LA;
            $payment->save();

            $recurrence->current_charge = $parcel_number;
            $recurrence->last_payment = $result->createdAt;
            $recurrence->card_id = $creditCard->id;
            $recurrence->save();

            try {
                $this->subscriptionService->enableSubscriptionByPayment($payment);
            }
            catch(Exception $e) {}

            return response()->json(null, 204);
        } catch (APIException $e) {
            $this->paymentFailed($payment, $payment->plans);

            Log::error(json_encode($e->getResponseBody()));

            return response()->json($e->getMessage(), 400);
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
        $creditCardId = $request->credit_card_id;
        $paymentId = $request->payment_id;

        $creditCard = CreditCard::where('subscriber_id', '=', JwtPlatformFacade::getSubscriber()->id)
            ->where('id', '=', $creditCardId)
            ->firstOrFail();

        /** @var Payment $payment */
        $payment = Payment::with('plans')->findOrFail($paymentId);

        $invalid = [
            Payment::STATUS_PAID,
            Payment::STATUS_CANCELED,
            // Payment::STATUS_PENDING,
        ];
        if (in_array($payment->status, $invalid)) {
            return response()->json('Pagamento já realizado ou cancelado', 400);
        }

        try {
            $orderRequest = new CreateOrderRequest();
            $orderRequest->closed = true;
            $orderRequest->customerId = $payment->customer_id;

            $orderRequest->metadata['obs'] = "Venda sem limite (parcela {$payment->installment_number} de {$payment->installments})";
            $orderRequest->metadata['payment_source'] = 'L';
            $orderRequest->metadata['unlimited_sale'] = true;
            $orderRequest->metadata['total_installments'] = $payment->installments;
            if (strlen($payment->coupon_id)) {
                $orderRequest->metadata['cupom_id'] = $payment->coupon_id;
            }

            $items = array();
            foreach ($payment->plans as $cod => $plan) {
                $itemPrice = new CreateOrderItemRequest();
                $itemPrice->description = $plan->name;
                $itemPrice->quantity = 1;
                $itemPrice->amount = str_replace('.', '', $plan->getPrice());
                $itemPrice->code = $plan->id;
                $items[] = $itemPrice;

                if (($plan->pivot->type == 'order_bump') ||
                    (!empty($orderbumpId) && $orderbumpId == $plan->id)
                ) {
                    $orderRequest->metadata['order_bump_plan_id'] = $plan->id;
                    $orderRequest->metadata['order_bump_plan'] = $plan->name;
                    $orderRequest->metadata['order_bump_value'] = $plan->price;
                } else {
                    $orderRequest->metadata['plan_id'] = $plan->id;
                    $orderRequest->metadata['plan'] = $plan->name;
                    $orderRequest->metadata['value'] = $plan->price;
                }
            }
            $orderRequest->items = $items;

            $paymentRequest = new CreatePaymentRequest();
            $paymentRequest->paymentMethod = 'credit_card';
            $paymentRequest->amount = str_replace('.', '', $payment->price);
            $paymentRequest->creditCard = new CreateCreditCardPaymentRequest();
            $paymentRequest->creditCard->cardId = $creditCard->card_id;

            $splitService = new SplitService($payment->platform_id);
            $split = $splitService->getPaymentSplit(
                $payment->price,
                $payment->plans_value,
                $payment->installments,
                false
            );
            $paymentRequest->split = $split;
            $paymentRequest->metadata = $splitService->getPaymentMetadata();
            $orderRequest->payments = array($paymentRequest);

            $mundipaggService = new MundipaggService($payment->platform_id);
            $order = $mundipaggService->getClient()->getOrders()->createOrder($orderRequest);

            $payment->gateway = 'mundipagg';
            $payment->status = $order->status;
            $payment->order_id = $order->id;
            $payment->customer_id = $order->customer->id;
            $payment->order_code = $order->code;

            if ($order->status != 'paid') {
                $this->paymentFailed($payment, $payment->plans);
                return response()->json('Não foi possível efetuar a operação', 400);
            }

            foreach ($order->charges as $cod => $charge) {
                $payment->charge_id = $charge->id;
                $payment->charge_code = $charge->code;
                $payment->payment_source = Payment::PAYMENT_SOURCE_LA;

                $splits = optional($charge->lastTransaction)->split ?? [];

                foreach ($splits as $c => $split) {
                    $value = $split->amount / 100;
                    if ($split->options->chargeProcessingFee) {
                        $payment->service_value = $value; //Xgrow
                    } else {
                        $payment->customer_value = $value; //Customer
                    }
                }
            }
            $payment->status = Payment::STATUS_PAID;
            $payment->save();
            $payment->plans()->syncWithoutDetaching($payment->plans);

            try {
                $this->subscriptionService->enableSubscriptionByPayment($payment);
            }
            catch(Exception $e) {}

            return response()->json(null, 204);
        } catch (APIException $e) {
            $this->paymentFailed($payment, $payment->plans);

            Log::error(json_encode($e->getResponseBody()));

            return response()->json($e->getMessage(), 400);
        }
    }

    private function paymentFailed(Payment $payment, $plans)
    {
        if ($payment->status != Payment::STATUS_PENDING) {
            $payment->status = Payment::STATUS_FAILED;
        }
        $payment->save();
        $payment->plans()->syncWithoutDetaching($plans);
    }

}
