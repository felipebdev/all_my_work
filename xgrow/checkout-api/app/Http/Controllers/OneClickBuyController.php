<?php

namespace App\Http\Controllers;

use App\Exceptions\Checkout\HashLockedException;
use App\Exceptions\Checkout\HashNotfoundException;
use App\Exceptions\RecipientFailedException;
use App\Facades\MagicToken;
use App\Http\Requests\Checkout\OneClickBuyRequest;
use App\Payment;
use App\Plan;
use App\Rules\SubscriptionActiveRule;
use App\Services\Finances\Checkout\CheckoutService;
use App\Services\Finances\Checkout\OneClickBuyService;
use App\Services\Finances\Objects\Constants;
use App\Services\Finances\Objects\OrderInfo;
use App\Services\Finances\Payment\Exceptions\FailedTransaction;
use App\Services\Finances\Payment\Exceptions\InvalidOrderException;
use App\Services\Finances\Payment\PaymentOrderFactory;
use App\Services\MundipaggService;
use App\Subscriber;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use MundiAPILib\APIException;
use MundiAPILib\Models\CreateOrderRequest;

class OneClickBuyController extends Controller
{
    private OneClickBuyService $oneClickBuyService;

    public function __construct(OneClickBuyService $oneClickBuyService)
    {
        $this->oneClickBuyService = $oneClickBuyService;
    }

    public function info(Request $request, $platform_id, $plan_id, $hash)
    {
        try {
            $oneClick = $this->oneClickBuyService->getOneClick($hash);
            $plan = Plan::findOrFail($plan_id);

            $data = [
                'plan_name' => $plan->name,
                'plan_value' => $plan->price,
                'payment_method' => $oneClick->payment_method,
            ];

            return response()->json($data);
        } catch (HashNotfoundException $e) {
            return response()->json('Token invalid or gone', Response::HTTP_NOT_FOUND);
        }
    }

    public function buy(OneClickBuyRequest $request, $platform_id, $plan_id, $hash)
    {
        try {
            $oneClick = $this->oneClickBuyService->useOneClick($hash);
        } catch (HashLockedException $e) {
            return response()->json(['message' => 'Código de compra sendo utilizado, por favor aguarde.'], Response::HTTP_CONFLICT);
        } catch (HashNotfoundException $e) {
            return response()->json(['message' => 'Código de compra inválido ou já utilizado'], Response::HTTP_NOT_FOUND);
        }

        try {
            $subscriber = $oneClick->subscriber;

            $plan = Plan::findOrFail($plan_id);
            $platform = $plan->platform;

            Validator::make(['subscription' => $plan_id], [
                'subscription' => [
                    new SubscriptionActiveRule($platform->id, $subscriber->id, $plan_id)
                ]
            ])->validate();

            $isRecurrence = $plan->type_plan == 'R';

            $installmentsAllowed = $isRecurrence ? 1 : $plan->installment; // max installments set on plan
            $minimumInstallments = $plan->price / 5; // R$5,00 per installment
            $previousOneClickBuy = $oneClick->installments;

            // force number of installments
            $oneClick->installments = min($installmentsAllowed, $minimumInstallments, $previousOneClickBuy);

            $orderInfo = OrderInfo::fromOneClick($plan_id, $oneClick);;

            $subscriberExistsInPlansPlatform = Subscriber::where('platform_id', $platform->id)
                ->where('email', $subscriber->email)
                ->exists();

            if (!$subscriberExistsInPlansPlatform) {
                // Replicate the subscriber in plan's platform if not exists
                $subscriber = $subscriber->replicate();
                $subscriber->platform_id = $platform->id;
                $subscriber->created_at = Carbon::now();
            }

            $subscriber->plan_id = $plan->id;
            $subscriber->save();

            $paymentMethod = $orderInfo->getPaymentMethod();
            $isCreditCard = $paymentMethod == Constants::XGROW_CREDIT_CARD;

            $hasRegisteredCreditCard = strlen($subscriber->credit_card_id) > 0;
            if ($isCreditCard && !$hasRegisteredCreditCard) {
                throw new InvalidOrderException('Nenhum cartão de crédito encontrado para o assinante');
            }

            $orderRequest = new CreateOrderRequest();
            $orderRequest->customerId = $subscriber->customer_id;

            if ($paymentMethod == Constants::XGROW_PIX) {
                $mundipaggService = new MundipaggService();
                $orderRequest->customer = $mundipaggService->getClient()->getCustomers()->getCustomer($subscriber->customer_id);
            }

            $paymentOrder = PaymentOrderFactory::getPaymentMethod($paymentMethod);
            $orderResult = $paymentOrder->order($orderInfo, $subscriber);

            $orderResponse = $orderResult->getMundipaggOrderResponse();

            //order paid or pending when not closed.
            if ($orderResponse) {
                $checkoutService = new CheckoutService();
                $checkoutService->confirmCheckout($orderInfo, $subscriber, $orderResult);

                //Change subscriber plan
                $subscriber->plan_id = $plan_id;
                $subscriber->save();
            }

            $this->oneClickBuyService->successfullOneClick($hash);

            // gateway's card_id if credit card was used
            $cardId = $paymentMethod == Constants::XGROW_CREDIT_CARD ? $subscriber->creditCard->card_id : null;

            $newOneClick = OneClickBuyService::createOneClickForSubscriber(
                $subscriber,
                $paymentMethod,
                $cardId,
                $oneClick->installments,
                $oneClick->id
            );

            //Return payment infos
            $query = Payment::where('payments.order_id', '=', $orderResponse->id)->get([
                'status', 'order_code', 'boleto_pdf', 'boleto_qrcode', 'boleto_barcode', 'boleto_url', 'boleto_line',
                'pix_qrcode', 'pix_qrcode_url'
            ]);

            foreach ($query as $c => $line) {
                $query[$c]->one_click = $newOneClick->id;
                $query[$c]->magicToken = MagicToken::generate($platform->id, $subscriber->id);
            }
            return response()->json($query);
        } catch (APIException $e) {
            Log::error($e);
            //The request is invalid. check fields
            if ($e->getContext()->getResponse()->getStatusCode() == 422) {
                $errors[] = Constants::XGROW_MESSAGE_INVALID_PARAMS;
            } else {
                $errors[] = $e->getMessage();
            }
            Log::error(json_encode($e));
            return response()->json(['message' => implode('\n', $errors)], 400);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->validator->errors()], 400);
        } catch (RecipientFailedException $e) {
            return response()->json(['message' => $e->getMessage(), 'failures' => $e->getFailures()], 400);
        } catch (FailedTransaction $e) {
            return response()->json(['message' => $e->getMessage(), 'failures' => $e->getFailures()], 400);
        } catch (Exception $e) {
            Log::error($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
