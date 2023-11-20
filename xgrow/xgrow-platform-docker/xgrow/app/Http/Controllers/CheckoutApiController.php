<?php

namespace App\Http\Controllers;

use App\Plan;
use App\PlanResources;
use App\Product;
use App\Services\MundipaggService;
use Exception;
use App\Payment;
use App\Platform;
use App\Subscriber;
use App\Integration;
use App\Subscription;
use App\CheckoutAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use MundiAPILib\APIException;
use App\Services\EmailService;
use Illuminate\Support\Facades\Log;
use MundiAPILib\Models\CreateOrderRequest;
use App\Http\Requests\Checkout\UpsellRequest;
use App\Http\Requests\Checkout\CheckoutRequest;
use App\Services\Mundipagg\CheckoutOrderService;
use App\Http\Requests\Checkout\SubscriberRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Mundipagg\MundipaggCheckoutController;
use App\Rules\SubscriptionActiveRule;
use App\Utils\TriggerIntegrationJob;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CheckoutApiController extends Controller
{
    use TriggerIntegrationJob;

    public function saveSubscriber(SubscriberRequest $request)
    {
        $mundipaggCheckoutController = new MundipaggCheckoutController();
        $subscriber = $mundipaggCheckoutController->saveSubscriberData($request);
        return response()->json(CheckoutAttempt::generateToken($request, $subscriber->id));
    }

    public function getPlan($platform_id, $plan_id)
    {
        $productColumns = [
            'name',
            'description',
            'type',
            'support_email',
            'keywords',
            'analysis_status',
            'checkout_whatsapp',
            'checkout_email',
            'checkout_support',
            //'checkout_facebook_pixel',
            //'checkout_google_tag',
            'checkout_url_terms',
            'checkout_support_platform',
            'checkout_layout',
            'checkout_address',
            'double_email'
        ];

        $planColumns = [
            'id',
            'name',
            'status',
            'currency',
            'price',
            'freedays',
            'installment',
            'description',
            'message_success_checkout',
            'url_checkout_confirm',
            'type_plan',
            'payment_method_credit_card',
            'payment_method_boleto',
            'payment_method_pix',
            'payment_method_multiple_cards',
        ];

        $plan = Plan::findOrFail($plan_id);

        $product = Product::findOrFail($plan->product_id);

        $orderbumps = PlanResources::with(['plans', 'image'])
            ->where('platform_id', '=', $platform_id)
            ->where('type', '=', 'O')
            ->where('product_plan_id', '=', $plan->id)
            ->get();

        $upsells = PlanResources::with(['plans', 'image'])
            ->where('platform_id', '=', $platform_id)
            ->where('type', '=', 'U')
            ->where('product_plan_id', '=', $plan->id)
            ->get();

        $ob = $orderbumps->map(function ($item, $key) {
            return [
                'id' => $item->plans->id ?? null,
                'name' => $item->plans->name ?? null,
                'message' => $item->message ?? null,
                'recurrence' => $item->plans->recurrence ?? null,
                'currency' => $item->plans->currency ?? null,
                'price' => ( isset($item->plans) ? $item->plans->getPrice() : null ), //Check promotional price
                'discount' => $item->discount ?? null,
                'freedays' => $item->plans->freedays ?? null,
                'freedays_type' => $item->plans->fredays_type ?? null,
                'charge_until' => $item->plans->charge_until ?? null,
                'type_plan' => $item->plans->type_plan ?? null,
                'installment' => $item->plans->installment ?? null,
                'description' => $item->plans->description ?? null,
                'image_id' => $item->image_id ?? null,
                'image_url' => $item->image->filename ?? null,
                'message_success_checkout' => null
            ];
        })->toArray();

        $up = $upsells->map(function ($item, $key) {
            return [
                'id' => $item->id ?? null,
                'name' => $item->plans->name ?? null,
                'message' => $item->message ?? null,
                'recurrence'  => $item->plans->recurrence ?? null,
                'currency' => $item->plans->currency ?? null,
                'price'  => ( isset($item->plans) ? $item->plans->getPrice() : null ), //Check promotional price
                'discount' => $item->discount ?? null,
                'freedays' => $item->plans->freedays ?? null,
                'freedays_type' => $item->plans->freedays_type ?? null,
                'charge_until'  => $item->plans->charge_until ?? null,
                'type_plan' => $item->plans->type_plan ?? null,
                'installment' => $item->plans->installment ?? null,
                'description' => $item->plans->description ?? null,
                'video_url' => $item->video_url ?? null,
                'accept_event'=> $item->accept_event ?? null,
                'accept_url'=> $item->accept_url ?? null,
                'decline_event'=> $item->decline_event ?? null,
                'decline_url'=> $item->decline_url ?? null,
                'image_id' => $item->image_id ?? null,
                'image_url'  => $item->image->filename ?? null,
                'payment_method_credit_card' => $item->plans->payment_method_credit_card ?? null,
                'payment_method_boleto' => $item->plans->payment_method_boleto ?? null,
                'payment_method_pix' => $item->plans->payment_method_pix ?? null,
                'payment_method_multiple_cards'=> $item->plans->payment_method_multiple_cards ?? null,
                'message_success_checkout' => null
            ];
        })->toArray();

        $data = $plan->only($planColumns);
        $data['status'] = ( $product->status == '1' ? $data['status'] : $product->status ); //Check first product status, after plan
        $data['message_success_checkout'] = null;
        $data['price'] = $plan->getPrice(); //Check promotional price
        $data['hasCoupons'] = count($plan->coupons) > 0;

        $data['product'] = $product->only($productColumns);
        //FIXME fixed approved until adjusting process in back office
        $data['product']['analysis_status'] = 'approved';

        //Pixel
        $pixel = $this->getPixel($platform_id);
        $data['product']['checkout_facebook_pixel'] = $pixel->pixel_id ?? null;
        $data['product']['checkout_facebook_pixel_test_event_code'] = $pixel->pixel_test_event_code ?? null;
        $data['product']['checkout_facebook_pixel_options'] = $pixel->options ?? null;

        //Google Tag
        $google_ads = $this->getGoogleTag($platform_id);
        $data['product']['checkout_google_tag'] = $google_ads->adwords_id ?? null;
        $data['product']['checkout_google_tag_conversion_label'] = $google_ads->ads_conversion_label ?? null;

        //Product images
        $data['product']['image_id'] = $product->image->id ?? null;
        $data['product']['image_url'] = $product->image->filename ?? null;

        $data['order_bump'] = $ob;
        $data['upsell'] = $up;

        return response()->json($data);
    }

    private function getPixel($platform_id) {
        $facebookPixel = Integration::where('platform_id', $platform_id)
            ->where('id_integration', 'FACEBOOKPIXEL')
            ->where('flag_enable', 1)
            ->first();

        $pixel = null;
        if($facebookPixel && strlen($facebookPixel->source_token) > 0) {
            $pixel = json_decode($facebookPixel->source_token);
            $pixel->options = collect($pixel)->except([
                'pixel_id', 'pixel_token', 'pixel_test_event_code'
            ]);
        }
        return $pixel;
    }

    private function getGoogleTag($platform_id) {
        $googleAds = Integration::where('platform_id', $platform_id)
            ->where('id_integration', 'GOOGLEADS')
            ->where('flag_enable', 1)
            ->first();

        $google_ads = null;
        if($googleAds && strlen($googleAds->source_token) > 0) {
            $google_ads = json_decode($googleAds->source_token);
        }
        return $google_ads;
    }


    public function getPlatform($platform_id)
    {
        $platform = Platform::where('id', '=', $platform_id)->firstOrFail(['id', 'name', 'url', 'name_slug', 'url_official', 'pixel_id', 'google_tag_id']);
        return response()->json($platform);
    }

    public function getInstallmentValue(Request $request)
    {
        $return = array();
        if (strlen($request->total_value) > 0 && strlen($request->installment) > 0) {
            for ($i = 1; $i <= $request->installment; $i++) {
                $obj = new \stdClass();
                $obj->installment = $i;
                $obj->value = number_format(Plan::getInstallmentValue($request->total_value, $i), 2);
                $return[] = $obj;
            }
        }
        return response()->json($return);
    }

    /**
     * Save checkout
     * @param Request $request
     * @param $platform_id
     * @param $plan_id
     * @param null $course_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkout(CheckoutRequest $request)
    {
        $mundipaggCheckoutController = new MundipaggCheckoutController();
        try {
            Validator::make(
                ['subscription' => $request->plan_id],
                ['subscription' => [
                    new SubscriptionActiveRule(
                        $request->platform_id,
                        $request->subscriber_id,
                        $request->plan_id,
                        $request->order_bump
                    )]
                ]
            )->validate();

            //Set order bumps
            $mundipaggCheckoutController->setOrderBumps($request->order_bump);
            //Process checkout
            $result = $mundipaggCheckoutController->process($request);
            //Return payment infos
            $query = Payment::where('payments.order_id', '=', $result->id)->get(['status', 'order_code', 'boleto_pdf', 'boleto_qrcode', 'boleto_barcode', 'boleto_url', 'boleto_line', 'pix_qrcode', 'pix_qrcode_url']);
            foreach ($query as $c=>$line) {
                $query[$c]->magicToken = Crypt::encrypt(['platform_id'=>$request->platform_id,'subscriber_id'=>$request->subscriber_id]);
            }
            return response()->json($query);

        } catch (APIException $e) {

            Log::error($e);
            //The request is invalid. check fields
            if ($e->getContext()->getResponse()->getStatusCode() == 422) {
                $errors[] = MundipaggCheckoutController::MESSAGE_INVALID_PARAMS;
            } else {
                $errors[] = $e->getMessage();
            }
            Log::error(json_encode($e));
            return response()->json(['message' => implode('\n', $errors)], 400);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->validator->errors()], 400);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Save upsell
     * @param Request $request
     * @param $platform_id
     * @param $plan_id
     * @param null $course_id
     */
    public function upSell(UpsellRequest $request, $platform_id)
    {
        try {
            $subscriber = Subscriber::findOrFail($request->subscriber_id);
            $platform = Platform::findOrFail($platform_id);
            $checkoutPlan = Plan::findOrFail($request->plan_id);

            $upsell = PlanResources::where('product_plan_id', $request->plan_id)->where('type', PlanResources::TYPE_UPSELL)->first();
            if( !isset($upsell) ) {
                throw new Exception("Upsell não cadastrado para o produto");
            }
            $plan = Plan::findOrFail($upsell->plan_id);

            Validator::make(
                ['subscription' => $plan->id],
                ['subscription' => [
                    new SubscriptionActiveRule(
                        $platform_id,
                        $request->subscriber_id,
                        $plan->id,
                        null
                    )]
                ]
            )->validate();

            $request->merge(['plan_id'=>$plan->id]);

            //if recurrence accept only credit_card
            if ( $request->payment_method != MundipaggCheckoutController::PAYMENT_METHOD_CREDIT_CARD && $plan->type_plan == Plan::PLAN_TYPE_SUBSCRIPTION ) {
                throw new Exception("Somente cartão de crédito é aceito para produtos do tipo assinatura");
            }

            //Change subscriber plan to save history
            $subscriber->plan_id = $plan->id;
            $subscriber->save();

            $orderRequest = new CreateOrderRequest();
            $orderRequest->customerId = $subscriber->customer_id;

            if ( $request->payment_method == MundipaggCheckoutController::PAYMENT_METHOD_CREDIT_CARD ) {
                if (!strlen($subscriber->credit_card_id) > 0) {
                    throw new Exception("Nenhum cartão de crédito encontrado para o assinante");
                }
            }

            if( $request->payment_method == MundipaggCheckoutController::PAYMENT_METHOD_PIX )
            {
                $orderService = new \App\Http\Controllers\Pagarme\CheckoutOrderService();
                $mundipaggService = new MundipaggService($platform_id);
                $orderRequest->customer = $mundipaggService->getClient()->getCustomers()->getCustomer($subscriber->customer_id);
            }
            else
            {
                $orderService = new CheckoutOrderService();
            }

            $mundipaggCheckoutController = new MundipaggCheckoutController();
            $result = $mundipaggCheckoutController->createOrder($orderRequest, $platform, $plan, $request, $orderService, $subscriber);
            //order paid or pending when not closed.
            if ($result) {
                $mundipaggCheckoutController->confirmCheckout($orderService, $subscriber, $plan, $request, $subscriber->customer_id, $result);

                //Change subscriber plan
                $subscriber->plan_id = $request->plan_id;
                $subscriber->save();

            }

            $query = Payment::where('payments.order_id', '=', $result->id)->get(['status', 'order_code', 'boleto_pdf', 'boleto_qrcode', 'boleto_barcode', 'boleto_url', 'boleto_line', 'pix_qrcode', 'pix_qrcode_url']);
            foreach ($query as $c=>$line) {
                $query[$c]->magicToken = Crypt::encrypt(['platform_id'=>$platform_id,'subscriber_id'=>$request->subscriber_id]);
            }
            return response()->json($query);

        } catch (APIException $e) {

            Log::error($e);
            //The request is invalid. check fields
            if ($e->getContext()->getResponse()->getStatusCode() == 422) {
                $errors[] = MundipaggCheckoutController::MESSAGE_INVALID_PARAMS;
            } else {
                $errors[] = $e->getMessage();
            }
            Log::error(json_encode($e));
            return response()->json(['message' => implode('\n', $errors)], 400);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->validator->errors()], 400);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * List all platforms
     * @return \Illuminate\Http\JsonResponse
     */
    public function listPlatforms()
    {
        return response()->json(Platform::all('id'));
    }

    /**
     * List all platform plans
     * @param $platform_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function listPlans($platform_id)
    {
        return response()->json(Plan::where('platform_id', $platform_id)->get('id'));
    }


    public function webhookOrderPaid(Request $request)
    {
        if ($request->type !== 'charge.paid' ) {
            return response()->json(['status' => 'error', 'message' => 'Payload not found'],400);
        }

        if( !in_array($request->data['payment_method'], array(MundipaggCheckoutController::PAYMENT_METHOD_BOLETO, MundipaggCheckoutController::PAYMENT_METHOD_PIX)) ) {
            return response()->json(['status' => 'success', 'message' => 'Somente são processados pagamentos do tipo boleto bancário'],200);
        }

        $return = true;
        $payment = Payment::where('charge_id', '=', $request->data['id'])
            ->where('order_code', '=', $request->data['order']['code'])->first();

        if( $payment ) {
            //Update payment
            $payment->status = $request->data['status'];
            $payment->save();

            //Confirm subscriber status
            if( $request->data['status'] == Payment::STATUS_PAID ) {
                $subscriber = Subscriber::findOrFail($payment->subscriber_id);
                $subscriber->status = Subscriber::STATUS_ACTIVE;
                $subscriber->save();

                foreach ($payment->plans as $cod=>$plan) {
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
        return response()->json(['status' => 'error', 'message'=>'Pagamento não encontrado'], 400);
    }

    /**
     * Check valid cupom code
     * @param $checkCupom
     */
    public function checkCupom(Request $request, $cupom) {
        $coupon = CouponController::findCoupon($request->platform_id, $request->plan_id, $cupom);
        if( $coupon ) {
            if( CouponController::isAvailable($coupon, $request->email) ) {
                return response()->json($coupon->find($coupon->id, ['description', 'value_type', 'value']));
            }
            else
            {
                $message = "Cupom não disponível";
            }

        }
        return response()->json(['status' => 'not-found', 'message' => $message ?? 'Cupom não encontrado'], 400);
    }

    /***
     * Download boleto pdf file
     * @param $order_code - Order code
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadBoleto($order_code) {
        $payment = Payment::where('order_code', $order_code)->first();
        if( $payment ) {
            $client = new \GuzzleHttp\Client();
            $response = $client->get($payment->boleto_pdf);
            $stream = $response->getBody();
            return response($stream->getContents(), 200, [
                'Content-Type' => $response->getHeader('Content-Type')[0],
            ]);
        }
        return response()->json(['message' => 'Arquivo não encontrado'],400);
    }

    /**
     * Check if subscriber can buy plan
     * @param Request $request
     */
    public function checkPlan($plan_id, Request $request) {
        try {
            $decrypt = Crypt::decrypt($request->header('token'));
            $found = Subscription::where('subscriber_id', $decrypt['subscriber_id'])->where('status', Subscription::STATUS_ACTIVE)->where('plan_id', $plan_id)->exists();
            if( $found ) {
                return response()->json(['message' => 'Você já possui este produto ativo'], 400);
            }
            return response()->json(['message' => 'Autorizado'], 200);
        }
        catch (\Exception $e) {
            return response()->json(['status' => 'Unauthorized user'], 401);
        }
    }
}
