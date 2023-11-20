<?php

namespace App\Http\Controllers;

use App\CheckoutAttempt;
use App\Exceptions\CaptureFailedException;
use App\Exceptions\RecipientFailedException;
use App\Facades\MagicToken;
use App\Http\Requests\Checkout\CheckoutRequest;
use App\Http\Requests\Checkout\SubscriberRequest;
use App\Http\Requests\Checkout\UpsellRequest;
use App\Http\Requests\LeadRequest;
use App\Payment;
use App\Plan;
use App\PlanResources;
use App\Platform;
use App\Product;
use App\Rules\SubscriptionActiveRule;
use App\Services\EmailService;
use App\Services\Finances\Checkout\CheckoutService;
use App\Services\Finances\Checkout\OneClickBuyService;
use App\Services\Finances\Objects\Constants;
use App\Services\Finances\Objects\OrderInfo;
use App\Services\Finances\Payment\Exceptions\FailedTransaction;
use App\Services\Finances\Payment\Exceptions\InvalidOrderException;
use App\Services\Finances\Payment\PaymentOrderFactory;
use App\Services\Finances\Recipient\RecipientsPlanService;
use App\Services\Finances\SubscriberService;
use App\Services\Integrations\FacebookPixelRepository;
use App\Services\Integrations\GoogleAdsRepository;
use App\Services\MundipaggService;
use App\Subscriber;
use App\Subscription;
use App\Utils\TriggerIntegrationJob;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use MundiAPILib\APIException;
use MundiAPILib\Models\CreateOrderRequest;
use Ramsey\Uuid\Uuid;
use stdClass;

class CheckoutApiController extends Controller
{
    use TriggerIntegrationJob;

    private LeadService $leadService;

    private RecipientsPlanService $recipientsPlanService;

    public function __construct(LeadService $leadService, RecipientsPlanService $recipientsPlanService)
    {
        $this->leadService = $leadService;
        $this->recipientsPlanService = $recipientsPlanService;
    }

    /**
     * Save person data and subscription (step 1)
     */
    public function saveSubscriber(SubscriberRequest $request)
    {
        /** @var SubscriberService $subscriberService */
        $subscriberService = resolve(SubscriberService::class);
        $subscriber = $subscriberService->saveSubscriberData($request);

        $this->leadService->createLeadFromSubscriber($subscriber);

        return response()->json(CheckoutAttempt::generateToken($request, $subscriber->id));
    }

    public function lead(LeadRequest $request)
    {
        $subscriber = Subscriber::findOrFail($request->subscriber_id);
        $subscriber->platform_id = $request->platform_id;
        $subscriber->plan_id = $request->plan_id;
        // do NOT save subscriber

        $this->leadService->createLeadFromSubscriber($subscriber, $request->type);

        return response()->noContent();
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
            'payment_method_free',
            'currency',
            'price',
            'freedays',
            'freedays_type',
            'charge_until',
            'installment',
            'description',
            'message_success_checkout',
            'url_checkout_confirm',
            'type_plan',
            'payment_method_credit_card',
            'payment_method_boleto',
            'payment_method_pix',
            'payment_method_multiple_cards',
            'use_promotional_price',
            'recurrence_description',
            'promotional_periods',
            'promotional_price'
        ];

        $plan = Plan::findOrFail($plan_id);

        $product = Product::findOrFail($plan->product_id);

        $platform = Platform::with('client:id,fantasy_name,email,first_name,last_name')->findOrFail($platform_id);

        $orderbumps = PlanResources::with(['plans.product', 'image'])
            ->where('platform_id', '=', $platform_id)
            ->where('type', '=', 'O')
            ->where('product_plan_id', '=', $plan->id)
            ->get();

        $upsells = PlanResources::with(['plans.product', 'image'])
            ->where('platform_id', '=', $platform_id)
            ->where('type', '=', 'U')
            ->where('product_plan_id', '=', $plan->id)
            ->get();

        $ob = $orderbumps->map(function ($item, $key) {
            return [
                'id' => $item->plans->id ?? null,
                'name' => $item->plans->name ?? null,
                'message' => $item->message ?? null,
                'payment_method_free' => $item->plans->payment_method_free ?? null,
                'recurrence' => $item->plans->recurrence ?? null,
                'currency' => $item->plans->currency ?? null,
                'price' => (isset($item->plans) ? $item->plans->getPrice() : null), //Check promotional price
                'original_price' => $item->plans->price ?? null,
                'discount' => $item->discount ?? null,
                'freedays' => $item->plans->freedays ?? null,
                'freedays_type' => $item->plans->freedays_type ?? null,
                'charge_until' => $item->plans->charge_until ?? null,
                'type_plan' => $item->plans->type_plan ?? null,
                'installment' => $item->plans->installment ?? null,
                'description' => $item->plans->description ?? null,
                'image_id' => $item->image_id ?? null,
                'image_url' => $item->image->filename ?? null,
                'message_success_checkout' => null,
                'learning_area_type' => $this->getLearningAreaType($item->plans->product),
                'use_promotional_price' => $item->plans->use_promotional_price ?? null,
                'recurrence_description' => $item->plans->recurrence_description ?? null,
                'promotional_periods' => $item->plans->promotional_periods ?? null,
                'promotional_price' => $item->plans->promotional_price ?? null
            ];
        })->toArray();

        $up = $upsells->map(function ($item, $key) {
            return [
                'id' => $item->id ?? null,
                'name' => $item->plans->name ?? null,
                'message' => $item->message ?? null,
                'payment_method_free' => $item->plans->payment_method_free ?? null,
                'recurrence' => $item->plans->recurrence ?? null,
                'currency' => $item->plans->currency ?? null,
                'price' => (isset($item->plans) ? $item->plans->getPrice() : null), //Check promotional price
                'original_price' => $item->plans->price ?? null,
                'discount' => $item->discount ?? null,
                'freedays' => $item->plans->freedays ?? null,
                'freedays_type' => $item->plans->freedays_type ?? null,
                'charge_until' => $item->plans->charge_until ?? null,
                'type_plan' => $item->plans->type_plan ?? null,
                'installment' => $item->plans->installment ?? null,
                'description' => $item->plans->description ?? null,
                'video_url' => $item->video_url ?? null,
                'accept_event' => $item->accept_event ?? null,
                'accept_url' => $item->accept_url ?? null,
                'decline_event' => $item->decline_event ?? null,
                'decline_url' => $item->decline_url ?? null,
                'image_id' => $item->image_id ?? null,
                'image_url' => $item->image->filename ?? null,
                'payment_method_credit_card' => $item->plans->payment_method_credit_card ?? null,
                'payment_method_boleto' => $item->plans->payment_method_boleto ?? null,
                'payment_method_pix' => $item->plans->payment_method_pix ?? null,
                'payment_method_multiple_cards' => $item->plans->payment_method_multiple_cards ?? null,
                'message_success_checkout' => null,
                'learning_area_type' => $this->getLearningAreaType($item->plans->product),
                'use_promotional_price' => $item->plans->use_promotional_price ?? null,
                'recurrence_description' => $item->plans->recurrence_description ?? null,
                'promotional_periods' => $item->plans->promotional_periods ?? null,
                'promotional_price' => $item->plans->promotional_price ?? null
            ];
        })->toArray();

        $data = $plan->only($planColumns);


        $orderBumpsContainsSubscription = $orderbumps->contains(function($item, $key) {
            return $item->plans->type_plan == Plan::PLAN_TYPE_SUBSCRIPTION;
        });

        if ($orderBumpsContainsSubscription) {
            $data['payment_method_multiple_cards'] = false;
        }

        $status = '0';
        $isClientVerified = $plan->platform->client->verified ?? false;
        if ($isClientVerified && $product->status == '1') {
            $status = $plan->status;
        }

        $data['status'] = $status;
        $data['message_success_checkout'] = null;
        $data['price'] = $plan->getPrice(); //Check promotional price
        $data['original_price'] = $plan->price ?? null;
        $data['hasCoupons'] = count($plan->coupons) > 0;

        $data['product'] = $product->only($productColumns);
        //FIXME fixed approved until adjusting process in back office
        $data['product']['analysis_status'] = 'approved';

        $data['product']['learning_area_type'] = $this->getLearningAreaType($product);

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

        $data['client'] = [
            'name' => $platform->client->fantasy_name ?? $platform->client->first_name.' '.$platform->client->last_name ?? null,
            'email' => $platform->client->email ?? null,
        ];

        $data['order_bump'] = $ob;
        $data['upsell'] = $up;

        $recipientsStatus = $this->recipientsPlanService->getActorsRecipientsForPlans($platform->id, [$plan->id]);
        $data['recipients'] = $recipientsStatus;

        $fatalErrors = count($recipientsStatus['client_errors']) + count($recipientsStatus['producers_errors']);
        if ($fatalErrors > 0) {
            $data['status'] = 0; // disable
        }

        return response()->json($data);
    }

    private function getLearningAreaType(Product $product): ?string
    {
        if ($product->only_sell) {
            return 'only_sell';
        } elseif ($product->external_learning_area) {
            return 'external';
        } elseif ($product->internal_learning_area) {
            return 'internal';
        }

        return null;
    }

    private function getPixel($platformId): stdClass
    {
        /** @var FacebookPixelRepository $repository */
        $repository = app()->make(FacebookPixelRepository::class);

        $facebookPixel = $repository->loadFacebookPixelFromDatabase($platformId);

        $pixel = new stdClass();

        if (!$facebookPixel) {
            return $pixel;
        }

        if ($facebookPixel->type === 'facebookpixel') {
            // new
            $pixel->pixel_id = $facebookPixel->api_account ?? null;
            $pixel->pixel_test_event_code = $facebookPixel->metadata['test_event_code'] ?? null;
            $pixel->options = [
                "fb_checkout_visit" => $facebookPixel->metadata['checkout_visit'] ?? 'false',
                "fb_sales_conversion" => $facebookPixel->metadata['sales_conversion'] ?? 'false',
                "fb_all_payment_methods" => $facebookPixel->metadata['payment_method'] == 'all_payment_methods' ? 'true' : 'false',
            ];

        } else {
            // legacy
            $legacy = json_decode($facebookPixel->source_token);

            $pixel->pixel_id = $legacy->pixel_id;
            $pixel->pixel_test_event_code = $legacy->pixel_test_event_code ?? null;
            $pixel->options = collect($legacy)->only([
                'fb_checkout_visit', 'fb_sales_conversion', 'fb_all_payment_methods'
            ])->toArray();
        }

        return $pixel;
    }

    private function getGoogleTag($platformId): stdClass
    {
        $repository = app()->make(GoogleAdsRepository::class);

        $googleAds = $repository->loadGoogleAdsFromDatabase($platformId);

        $ads = new stdClass();

        if (!$googleAds) {
            return $ads;
        }

        if ($googleAds->type === 'googleads') {
            // new
            $ads->adwords_id = $googleAds->metadata['adsId'] ?? null;
            $ads->ads_conversion_label = $googleAds->metadata['adsConversionLabel'] ?? null;
        } else {
            // legacy

            $sourceToken = $googleAds->source_token ?? '';
            if (strlen($sourceToken) <= 0) {
                return $ads;
            }

            $legacy = json_decode($sourceToken);

            $ads->adwords_id = $legacy->adwords_id;
            $ads->ads_conversion_label = $legacy->ads_conversion_label ?? null;
        }

        return $ads;
    }


    public function getPlatform($platform_id)
    {
        $platform = Platform::where('id', '=', $platform_id)->firstOrFail([
            'id', 'name', 'url', 'name_slug', 'url_official', 'pixel_id', 'google_tag_id'
        ]);
        return response()->json($platform);
    }

    public function getInstallmentValue(Request $request)
    {
        if (strlen($request->total_value) <= 0 || strlen($request->installment) <= 0) {
            return response()->json([]);
        }

        for ($i = 1; $i <= $request->installment; $i++) {
            $obj = new stdClass();
            $obj->installment = $i;
            $obj->value = number_format(Plan::getInstallmentValue($request->total_value, $i), 2);
            $return[] = $obj;
        }

        return response()->json($return);
    }

    /**
     * Save checkout
     * @param  Request  $request
     * @param $platform_id
     * @param $plan_id
     * @param  null  $course_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkout(CheckoutRequest $request)
    {
        $checkoutService = new CheckoutService();
        try {
            $orderInfo = OrderInfo::fromRequestData($request->validated());

            Validator::make(
                ['subscription' => $request->plan_id],
                [
                    'subscription' => [
                        new SubscriptionActiveRule(
                            $request->platform_id,
                            $request->subscriber_id,
                            $request->plan_id,
                            $request->order_bump ?? []
                        )
                    ]
                ]
            )->validate();

            if ($orderInfo->getPaymentMethod() == Payment::TYPE_PAYMENT_CREDIT_CARD) {
                $pendingOrdered = $this->leadService->getPendindOrderedCreditCard($orderInfo);
                if ($pendingOrdered) {
                    $correlationId = $request->header('X-Correlation-Id') ?? (string) Uuid::uuid4();

                    Log::warning('checkout:ordered:credit_card_error', [
                        'correlation_id' => $correlationId,
                        'subscriber_id' => $orderInfo->getSubscriberId(),
                        'plan_id' => $orderInfo->getAllPlanIds(),
                    ]);

                    /**@var \Carbon\Carbon $cartStatusUpdatedAt */
                    $cartStatusUpdatedAt = $pendingOrdered->cart_status_updated_at;

                    $date = $cartStatusUpdatedAt->format('d/m/Y');
                    $time = $cartStatusUpdatedAt->format('H:i:s');
                    $msg = "Já existe uma compra em processamento desse produto (iniciada dia {$date} às {$time}). ";
                    $msg .= "Por favor aguarde ou entre em contato com suporte caso considere um erro.";
                    $msg .= " [Código $correlationId]";

                    if ($cartStatusUpdatedAt < Carbon::now()->subMinutes(15)) {
                        // updated 15 minutes ago (or later)
                        $this->notifyToSentryIfBound($orderInfo);
                    }

                    return response()->json(['message' => $msg, 'failures' => []], Response::HTTP_CONFLICT);
                }
            }

            //Process checkout
            $subscriber = Subscriber::findOrFail($orderInfo->getSubscriberId());

            $orderResult = $checkoutService->process($orderInfo, $subscriber);

            /**
             * @var \MundiAPILib\Models\GetOrderResponse|\PagarmeCoreApiLib\Models\GetOrderResponse|null $orderResponse
             */
            $orderResponse = $orderResult->getOrderResponse();

            // Mundipagg uses most recent first, reverse to match user's input order
            $charges = collect($orderResponse->charges)->reverse();

            $firstCharge = $charges->first(); // use first charge
            $gatewayCardId = $firstCharge->lastTransaction->card->id ?? null;

            $isUnlimitedSale = $orderResponse->metadata['unlimited_sale'] ?? false;
            if ($isUnlimitedSale) {
                $installments = $orderResponse->metadata['total_installments'] ?? 1; // save original installments
            } else {
                $installments = $firstCharge->lastTransaction->installments ?? 1; // save installments of last transaction
            }

            $oneClick = OneClickBuyService::createOneClickForSubscriber(
                $subscriber,
                $orderInfo->getPaymentMethod(),
                $gatewayCardId,
                $installments
            );

            //Return payment infos
            $query = Payment::where('payments.order_id', '=', $orderResponse->id)->get([
                'status', 'order_code', 'boleto_pdf', 'boleto_qrcode', 'boleto_barcode', 'boleto_url', 'boleto_line',
                'pix_qrcode', 'pix_qrcode_url'
            ]);
            foreach ($query as $c => $line) {
                $query[$c]->one_click = $oneClick->id;
                $query[$c]->magicToken = MagicToken::generate($request->platform_id, $request->subscriber_id);
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
            $msg = "Não foi possível realizar a venda. Contate o suporte do produtor para mais informações: " . $e->getMessage();
            return response()->json(['message' => $msg, 'failures' => $e->getFailures()], 400);
        } catch (FailedTransaction $e) {
            return response()->json(['message' => $e->getMessage(), 'failures' => $e->getFailures()], 400);
        } catch (InvalidOrderException | Exception $e) {
            Log::error($e);

            if (app()->bound('sentry')) {
                app('sentry')->captureException($e);
            }

            return response()->json(['message' => $e->getMessage()], 400);
        } catch (CaptureFailedException $e) {
            return response()->json(['message' => 'Não foi possível finalizar a transação. Tente novamente. (409)'], 409);
        }
    }

    /**
     * Save upsell
     * @param  Request  $request
     * @param $platform_id
     * @param $plan_id
     * @param  null  $course_id
     */
    public function upSell(UpsellRequest $request, $platform_id)
    {
        try {
            $subscriber = Subscriber::findOrFail($request->subscriber_id);
            $platform = Platform::findOrFail($platform_id);
            $checkoutPlan = Plan::findOrFail($request->plan_id);

            $upsell = PlanResources::where('product_plan_id', $request->plan_id)
                ->where('type', PlanResources::TYPE_UPSELL)->first();
            if (!isset($upsell)) {
                throw new InvalidOrderException('Upsell não cadastrado para o produto');
            }
            $plan = Plan::findOrFail($upsell->plan_id);

            Validator::make(['subscription' => $plan->id], [
                'subscription' => [
                    new SubscriptionActiveRule($platform_id, $request->subscriber_id, $plan->id)
                ]
            ])->validate();

            $request->merge(['plan_id' => $plan->id]);

            $orderInfo = OrderInfo::fromRequestData($request->all());
            $orderInfo->setIsUpsell(true);

            $subscriptionPaymentMethods = [
                Constants::XGROW_CREDIT_CARD,
                Constants::XGROW_BOLETO,
                Constants::XGROW_PIX,
            ];

            $upsellIsSubscription = $plan->type_plan == Plan::PLAN_TYPE_SUBSCRIPTION;
            $acceptsSubscriptionPaymentMethod = in_array($orderInfo->getPaymentMethod(), $subscriptionPaymentMethods);
            if ($upsellIsSubscription && !$acceptsSubscriptionPaymentMethod) {
                throw new InvalidOrderException('Somente cartão de crédito, boleto e PIX são aceitos para produtos do tipo assinatura');
            }

            //Change subscriber plan to save history
            $subscriber->plan_id = $plan->id;
            $subscriber->save();

            $isCreditCard = $orderInfo->getPaymentMethod() == Constants::XGROW_CREDIT_CARD;
            $hasRegisteredCreditCard = strlen($subscriber->credit_card_id) > 0;
            if ($isCreditCard && !$hasRegisteredCreditCard) {
                throw new InvalidOrderException('Nenhum cartão de crédito encontrado para o assinante');
            }

            $orderRequest = new CreateOrderRequest();
            $orderRequest->customerId = $subscriber->customer_id;

            if ($orderInfo->getPaymentMethod() == Constants::XGROW_PIX) {
                $mundipaggService = new MundipaggService();
                $orderRequest->customer = $mundipaggService->getClient()->getCustomers()->getCustomer($subscriber->customer_id);
            }

            $paymentMethod = PaymentOrderFactory::getPaymentMethod($orderInfo->getPaymentMethod());
            $orderResult = $paymentMethod->order($orderInfo, $subscriber);

            $orderResponse = $orderResult->getMundipaggOrderResponse();

            //order paid or pending when not closed.
            if ($orderResponse) {
                $checkoutService = new CheckoutService();
                $checkoutService->confirmCheckout($orderInfo, $subscriber, $orderResult);

                //Change subscriber plan
                $subscriber->plan_id = $request->plan_id;
                $subscriber->save();
            }

            // Mundipagg uses most recent first, reverse to match user's input order
            $charges = collect($orderResponse->charges)->reverse();

            $firstCharge = $charges->first(); // use first charge
            $gatewayCardId = $firstCharge->lastTransaction->card->id ?? null;
            $installments = $firstCharge->lastTransaction->installments ?? 1;

            $oneClick = OneClickBuyService::createOneClickForSubscriber(
                $subscriber,
                $orderInfo->getPaymentMethod(),
                $gatewayCardId,
                $installments
            );

            $query = Payment::where('payments.order_id', '=', $orderResponse->id)->get([
                'status', 'order_code', 'boleto_pdf', 'boleto_qrcode', 'boleto_barcode', 'boleto_url', 'boleto_line',
                'pix_qrcode', 'pix_qrcode_url'
            ]);
            foreach ($query as $c => $line) {
                $query[$c]->one_click = $oneClick->id;
                $query[$c]->magicToken = MagicToken::generate($platform_id, $request->subscriber_id);
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
        } catch (FailedTransaction $e) {
            return response()->json(['message' => $e->getMessage(), 'failures' => $e->getFailures()], 400);
        } catch (CaptureFailedException $e) {
            return response()->json(['message' => 'Não foi possível finalizar a transação. Tente novamente.'], 409);
        } catch (Exception $e) {
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

    /**
     * Check valid cupom code
     * @param $checkCupom
     */
    public function checkCupom(Request $request, $cupom)
    {
        $coupon = CouponController::findCoupon($request->platform_id, $request->plan_id, $cupom);
        if (!$coupon) {
            return response()->json(['status' => 'not-found', 'message' => 'Cupom não encontrado'], 400);
        }

        if (CouponController::isAvailable($coupon, $request->email)) {
            return response()->json($coupon->find($coupon->id, ['description', 'value_type', 'value']));
        }

        return response()->json(['status' => 'not-found', 'message' => 'Cupom não disponível'], 400);
    }

    /***
     * Download boleto pdf file
     * @param $order_code  - Order code
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadBoleto($order_code)
    {
        $payment = Payment::where('order_code', $order_code)->first();
        if (!$payment) {
            return response()->json(['message' => 'Arquivo não encontrado'], 400);
        }

        $client = new Client();
        $response = $client->get($payment->boleto_pdf);
        $stream = $response->getBody();
        return response($stream->getContents(), 200, [
            'Content-Type' => $response->getHeader('Content-Type')[0],
        ]);
    }

    /**
     * Check if subscriber can buy plan
     * @param  Request  $request
     */
    public function checkPlan($plan_id, Request $request)
    {
        try {
            $decrypt = Crypt::decrypt($request->header('token'));
            $found = Subscription::where('subscriber_id', $decrypt['subscriber_id'])
                ->where('status', Subscription::STATUS_ACTIVE)->where('plan_id', $plan_id)
                ->exists();
            if ($found) {
                return response()->json(['message' => 'Você já possui este produto ativo'], 400);
            }
            return response()->json(['message' => 'Autorizado'], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'Unauthorized user'], 401);
        }
    }

    private function notifyToSentryIfBound(OrderInfo $orderInfo): void
    {
        if (app()->bound('sentry')) {
            $history = $this->leadService->getLeadHistory($orderInfo);

            $orderCorrelationIds = $history->pluck('order_correlation_id')->join(';');

            $contextObject = ['order_correlation_ids' => $orderCorrelationIds];

            \Sentry\withScope(function (\Sentry\State\Scope $scope) use ($contextObject): void {
                $scope->setContext('lead_history', $contextObject);
                \Sentry\captureMessage('Bad order processing');
            });
        }
    }
}
