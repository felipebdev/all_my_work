<?php

namespace App\Http\Controllers\Mundipagg;

use App\Plan;
use App\Email;
use Exception;
use App\Client;
use App\Payment;
use App\Platform;
use Carbon\Carbon;
use App\Recurrence;
use App\Subscriber;
use App\Subscription;
use App\Mail\SendMailAuto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use MundiAPILib\APIException;
use App\Services\EmailService;
use App\Services\MundipaggService;
use Illuminate\Support\Facades\Log;
use MundiAPILib\Models\GetOrderResponse;
use MundiAPILib\Models\CreateOrderRequest;
use MundiAPILib\Models\CreatePhoneRequest;
use MundiAPILib\Models\CreatePhonesRequest;
use MundiAPILib\Models\CreateCustomerRequest;
use App\Services\Mundipagg\CheckoutOrderService;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Repositories\Contracts\SubscriptionRepositoryInterface;
use App\Services\Mundipagg\CheckoutUnlimitedSaleService;
use App\Utils\TriggerIntegrationJob;
use Illuminate\Support\Arr;

class MundipaggCheckoutController extends \App\Http\Controllers\Controller
{
    use TriggerIntegrationJob;

    private $transactionService;
    public $mundipaggService;
    public $orderBumps;

    const MESSAGE_PAYMENT_FAILED = 'Pagamento não autorizado. Verifique os dados do cartão e tente novamente';
    const MESSAGE_INVALID_PARAMS = 'Não foi possível finalizar a compra. Verifique os campos preenchidos e tente novamente';
    const CNPJ_ESTRANGEIROS = '09599048000132'; //Numero de CNPJ para identificar estrangeiros na mundipagg

    const PAYMENT_METHOD_CREDIT_CARD = 'credit_card';
    const PAYMENT_METHOD_BOLETO = 'boleto';
    const PAYMENT_METHOD_PIX = 'pix';

    public function getErrors($platform)
    {
        $client = Client::find($platform->customer_id);
        $errors = array();
        if (empty($client->percent_split)) {
            $errors[] = 'O campo Percentual a receber deve ser preenchido no cadastro do Cliente';
        }
        if (empty($client->bank)) {
            $errors[] = 'O campo Banco deve ser preenchido no cadastro do Cliente';
        }
        if (empty($client->branch)) {
            $errors[] = 'O campo Agência deve ser preenchido no cadastro do Cliente';
        }
        if (empty($client->account)) {
            $errors[] = 'O campo Conta deve ser preenchido no cadastro do Cliente';
        }
        return $errors;
    }

    /**
     * Save person data and subscription (step 1)
     */
    public function saveSubscriber(Request $request)
    {
        try {
            $subscriber = $this->saveSubscriberData($request);
            return response()->json($subscriber)->setStatusCode(200);
        } catch (\Exception $e) {

            return response($e->getMessage(), 400);
        }
    }

    public function saveSubscriberData(Request $request)
    {
        $subscriber = Subscriber::firstOrNew(
            ['platform_id' => $request->platform_id,
                'email' => $request->email]
        );
        $subscriber->name = $request->name;
        $subscriber->email = $request->email;
        if (strlen($request->password) > 0) {
            $subscriber->raw_password = $request->password;
        }
        $subscriber->main_phone = $request->main_phone;
        $subscriber->address_zipcode = normalizeZipCode($request->address_zipcode ?? '', $request->country);
        $subscriber->address_city = $request->address_city;
        $subscriber->address_district = $request->address_district;
        $subscriber->address_street = $request->address_street;
        $subscriber->address_number = $request->address_number;
        $subscriber->address_comp = $request->address_comp;
        $subscriber->platform_id = $request->platform_id;
        $subscriber->plan_id = $request->plan_id;
        $subscriber->address_state = $request->address_state;
        $subscriber->source_register = Subscriber::SOURCE_CHECKOUT;
        $subscriber->cel_phone = '(' . $request->phone_area_code . ') ' . $request->phone_number;
        $subscriber->phone_country_code = $request->phone_country_code;
        $subscriber->phone_area_code = $request->phone_area_code;
        $subscriber->phone_number = $request->phone_number;

        if ($request->document_type == 'passport') {
            //Utiliza CPF fixo para estrangeiros conforme regra na mundipaggg
            $subscriber->address_country = Subscriber::converCountryCode($request->country);
            $subscriber->document_number = $request->document_number ?? self::CNPJ_ESTRANGEIROS;
            $subscriber->tax_id_number = $request->document_number;
            $subscriber->document_type = 'CNPJ';
            $subscriber->type = Subscriber::TYPE_LEGAL_PERSON;
        } else {
            $subscriber->address_country = "BRA";
            $subscriber->document_number = $request->document_number;
            $subscriber->document_type = strtoupper($request->document_type);
            $subscriber->type = strtoupper($request->document_type) == Subscriber::DOCUMENT_TYPE_CPF ? Subscriber::TYPE_NATURAL_PERSON : Subscriber::TYPE_LEGAL_PERSON;
        }
        if (isset($subscriber->status)) {
            if ($subscriber->status != Subscriber::STATUS_ACTIVE) {
                $subscriber->status = Subscriber::STATUS_LEAD;
                $this->triggerLeadCreatedEvent($subscriber);
            }
        } else {
            $subscriber->status = Subscriber::STATUS_LEAD;
            $this->triggerLeadCreatedEvent($subscriber);
        }

        $subscriber->save();

        return $subscriber;
    }

    /**
     * @param Request $request
     * @param $platform_id
     * @param $plan_id
     * @param null $course_id
     * @return mixed
     * @throws APIException|\Exception
     */
    public function process(Request $request)
    {
        $platform = Platform::findOrFail($request->platform_id);
        $plan = Plan::findOrFail($request->plan_id);
        $subscriber = Subscriber::findOrFail($request->subscriber_id);

        $this->setMundipaggService(new MundipaggService($request->platform_id));

        //Salvar cliente
        $customer = $this->saveCustomer($subscriber, $request);

        $orderRequest = new CreateOrderRequest();
        $orderRequest->customerId = $customer->id;
        $orderRequest->customer = $customer;
        $subscriber->customer_id = $customer->id;

        if ($request->payment_method == self::PAYMENT_METHOD_PIX) {
            $orderService = new \App\Http\Controllers\Pagarme\CheckoutOrderService();
        } else {
            $orderService = new CheckoutOrderService();
        }

        //Add order bumps
        $orderService->setOrderBumps($this->getOrderBumps());

        //Create mundipagg order
        $result = $this->createOrder($orderRequest, $platform, $plan, $request, $orderService, $subscriber);

        //order paid or pending when not closed.
        if ($result) {
            $orderBumps = (is_array($this->orderBumps)) ? Arr::pluck($this->orderBumps, 'id') : [];
            $this->checkExpiredPayments($platform, $subscriber, $plan, $orderBumps);
            $this->expireOldPaymentsSameType($platform, $subscriber, $plan);
            $this->confirmCheckout($orderService, $subscriber, $plan, $request, $customer->id, $result);
        }

        return $result;
    }

    public function createOrder(CreateOrderRequest $orderRequest, Platform $platform, Plan $plan, Request $request, CheckoutOrderService $orderService, Subscriber $subscriber)
    {
        //Caso não for boleto, cria pedido de teste para validação do cartão
        if ($plan->freedays_type == 'free' && $plan->freedays > 0 && $request->payment_method == self::PAYMENT_METHOD_CREDIT_CARD) {
            //Save credit cards
            $creditCardController = new CreditCardController($this->mundipaggService);
            $creditCards = $creditCardController->saveCreditCards($subscriber, $request);
            foreach ($creditCards as $cod => $creditCard) {
                //Cria pedido de 5 reais para teste
                $result = $orderService->createTestOrder($orderRequest, $platform, $plan, $creditCard->card_id, $request);
                if ($result->status != 'paid') {
                    Log::error(json_encode($result));
                    throw new \Exception(MundipaggExceptionController::getMessage($result));
                }
                $orderRequest->closed = false;
                //Cancela pedido de teste
                $orderService->cancelCharge($result, $platform->id);
            }
        } else {
            $result = $orderService->createOrder($orderRequest, $platform, $plan, $request, $subscriber);
            if (!in_array($result->status, array('paid', 'pending'))) {
                Log::error(json_encode($result));
                MundipaggExceptionController::createFailedTransaction(
                    $platform->id,
                    $subscriber->id,
                    $result
                );

                throw new \Exception(MundipaggExceptionController::getMessage($result));
            }
        }
        return $result;
    }

    public function confirmCheckout(
        CheckoutOrderService $orderService,
        Subscriber $subscriber,
        Plan $plan,
        Request $request,
        $custommerId,
        GetOrderResponse $orderResponse
    )
    {
        $orderNumber = strtoupper(uniqid());
        $paymentDate = Carbon::now();
        if ($plan->freedays_type == Plan::FREE_DAYS_TYPE_FREE && $plan->freedays > 0) {
            $paymentDate = Carbon::now()->addDays($plan->freedays);
        }

        $platform = Platform::find($subscriber->platform_id);
        $clientTaxTransaction = ($platform) ? ($platform->client->tax_transaction ?? 1.5) : 1.5;

        //store payment
        $payment = $orderService->storePayment(
            Payment::PAYMENT_SOURCE_CHECKOUT,
            $subscriber,
            $orderResponse,
            $paymentDate,
            $orderNumber,
            $clientTaxTransaction,
            $this->getOrderBumps()
        );

        //Generate Unlimited sale pending payments
        if (isset($orderResponse->metadata)) {
            if (isset($orderResponse->metadata['unlimited_sale'])) {
                if ($orderResponse->metadata['unlimited_sale'] == true) {
                    //Saves remaining payments from the unlimited sale
                    foreach ($orderResponse->charges as $cod => $charge) {
                        for ($i = 1; $i < $orderResponse->metadata['total_installments']; $i++) {
                            //Payment date + 30 days
                            $paymentDate = $paymentDate->addDays(30);
                            CheckoutUnlimitedSaleService::storePendingPayment(
                                $subscriber,
                                $orderResponse,
                                $paymentDate,
                                $payment,
                                $i + 1,
                                $clientTaxTransaction,
                                $this->getOrderBumps()
                            );
                        }
                    }
                }
            }
        }

        //Create recurrence for subscription
        if ($plan->type_plan == Plan::PLAN_TYPE_SUBSCRIPTION && strlen($subscriber->credit_card_id) > 0) {
            //Create recurrence for a subscription
            $recurrenceController = new RecurrenceController();
            $recurrence = $recurrenceController->store(
                $subscriber->id,
                $plan->recurrence,
                $paymentDate,
                $subscriber->credit_card_id,
                $plan->id,
                1,
                Recurrence::TYPE_SUBSCRIPTION,
                null,
                $orderNumber
            );

            if ($orderResponse) {
                $payment->recurrences()->attach($recurrence);
            }
        }

        $return = true;
        //Boleto
        if ($request->payment_method == self::PAYMENT_METHOD_BOLETO) {
            //Status pending payment
            if ($subscriber->status != Subscriber::STATUS_ACTIVE) {
                $subscriber->status = Subscriber::STATUS_PENDING_PAYMENT;
            }

            $payment_pendent = date_format(now(), 'Y-m-d');
            $this->triggerBankSlipCreatedEvent($payment);

            //Send boleto mails
            foreach (Payment::where('payments.order_id', '=', $orderResponse->id)->get() as $cod => $payment) {
                //Send Boleto Mail
                $this->sendBoletoMail($subscriber, $payment);
            }

        } elseif ($request->payment_method == self::PAYMENT_METHOD_PIX) //PIX
        {
            //Status pending payment
            if ($subscriber->status != Subscriber::STATUS_ACTIVE) {
                $subscriber->status = Subscriber::STATUS_PENDING_PAYMENT;
            }
            $payment_pendent = date_format(now(), 'Y-m-d');
        } else //Credit card
        {
            //Confirm subscriber status
            $subscriber->status = Subscriber::STATUS_ACTIVE;
            $payment_pendent = null;
            $this->triggerPaymentApprovedEvent($payment);

            // Send new register mail
            try {
                $emailService = new EmailService();
                $return &= $emailService->sendMailPurchaseProofAfterCheckout(
                    $subscriber->platform,
                    $subscriber,
                    $payment
                );
            } catch (Exception $e) {
            }
        }

        //save subscription
        $this->saveSubscription($subscriber, $orderResponse->code, $payment_pendent, $orderNumber);
        //Save Mundipagg custommer id
        $subscriber->customer_id = $custommerId;
        $subscriber->save();

        return $return;
    }

    private function saveSubscription(
        Subscriber $subscriber,
        $code = null,
        $payment_pendent = null,
        $orderNumber = null
    )
    {
        $plans = array($subscriber->plan);
        $orderBumps = $this->getOrderBumps();
        if( is_array($orderBumps) ) {
            foreach ($orderBumps as $cod => $orerBumpPlan) {
                $plans[] = $orerBumpPlan;
            }
        }
        foreach ($plans as $cod => $plan) {
            $subscription = Subscription::firstOrNew([
                    'platform_id' => $subscriber->platform->id,
                    'plan_id' => $plan->id,
                    'subscriber_id' => $subscriber->id,
                    'canceled_at' => null,
                    'order_number' => $orderNumber
                ]
            );
            $subscription->platform_id = $subscriber->platform->id;
            $subscription->plan_id = $plan->id;
            $subscription->subscriber_id = $subscriber->id;
            $subscription->gateway_transaction_id = $code;
            $subscription->payment_pendent = $payment_pendent;
            $subscription->status = ($payment_pendent !== null)
                ? Subscription::STATUS_PENDING_PAYMENT
                : Subscription::STATUS_ACTIVE;
            $subscription->status_updated_at = \Carbon\Carbon::now();
            $subscription->order_number = $orderNumber ?? null;
            $subscription->save();
        }
    }

    private function saveCustomer(Subscriber $subscriber, Request $data)
    {
        try {

            $customer = new CreateCustomerRequest();
            $customer->name = $subscriber->name;

            if (strlen($subscriber->document_number) > 0) {
                //Empresa ou estrangeiro
                if ($subscriber->document_type == 'CNPJ') {
                    $customer->type = 'company';
                    //Caso não informado o CNPJ (estrangeiros) utiliza o CNPJ Padrão
                    if (strlen(preg_replace('/[^0-9]/', '', $subscriber->document_number)) != 14) {
                        $subscriber->document_number = null;
                    }
                    $customer->document = preg_replace('/[^0-9]/', '', $subscriber->document_number ?? self::CNPJ_ESTRANGEIROS);
                } else {
                    $customer->type = 'individual';
                    $customer->document = preg_replace('/[^0-9]/', '', $subscriber->document_number);
                }
            }
            $customer->email = $subscriber->email;
            $customer->phones = new CreatePhonesRequest();
            $customer->phones->mobilePhone = new CreatePhoneRequest();
            $customer->phones->mobilePhone->countryCode = $subscriber->phone_country_code;
            $customer->phones->mobilePhone->areaCode = $subscriber->phone_area_code;
            $customer->phones->mobilePhone->number = $subscriber->phone_number;
            if (strlen($data->address_zipcode) > 0) {
                $customer->address = AddressController::getAddress($data);
            }

            //Create or update customer
            $response = $this->mundipaggService->getClient()->getCustomers()->createCustomer($customer);
        } catch (APIException $e) {
            Log::error('CUSTOMER');
            Log::error(json_encode($customer));
            Log::error('EXCEPTION');
            Log::error(json_encode($e));
            Log::error('SUBSCRIBER');
            Log::error(json_encode($subscriber));
            Log::error('REQUEST');
            Log::error(json_encode($data->all()));
            throw new \Exception(self::MESSAGE_INVALID_PARAMS);
        }

        return $response;
    }

    public function sendBoletoMail(Subscriber $subscriber, Payment $payment)
    {
        $plan = $subscriber->plan;

        $emailData = [
            'subscriber' => true,
            'platform_id' => $subscriber->platform_id,
            'user' => $subscriber,
            'plan_name' => $plan->name,
            'email_id' => Email::CONSTANT_EMAIL_BOLETO,
            'boleto_barcode' => $payment->boleto_barcode,
            'boleto_qrcode' => $payment->boleto_qrcode,
            'boleto_pdf' => route('checkout.boleto.download', [$payment->order_code]),
            'boleto_url' => route('checkout.boleto.download', [$payment->order_code]),
            'order_code' => $payment->order_code,
            'price' => $payment->price,
            'plans' => ($payment->type === 'R') ? [$payment->recurrences[0]->plan] : $payment->plans,
        ];

        $usersTo = [$subscriber->email];

        EmailService::send($usersTo, new SendMailAuto($emailData));
    }

    /**
     * @return mixed
     */
    public function getMundipaggService()
    {
        return $this->mundipaggService;
    }

    /**
     * @param mixed $mundipaggService
     */
    public function setMundipaggService($mundipaggService): void
    {
        $this->mundipaggService = $mundipaggService;
    }

    /**
     * @return mixed
     */
    public function getOrderBumps()
    {
        return $this->orderBumps;
    }

    /**
     * @param mixed $orderBumps
     */
    public function setOrderBumps($orderBumps): void
    {
        if (is_array($orderBumps)) {
            $orderBumpPlans = array();
            foreach ($orderBumps as $order_bump_plan_id) {
                if( $order_bump_plan_id != null ) {
                    $orderBumpPlans[] = Plan::findOrFail($order_bump_plan_id);
                }
            }
            $this->orderBumps = $orderBumpPlans;
        }

    }

    private function checkExpiredPayments(
        Platform $platform,
        Subscriber $subscriber,
        Plan $plan,
        array $orderBumps
    )
    {
        try {
            $paymentRepository = app()->make(PaymentRepositoryInterface::class);
            $payments = $paymentRepository->getBySubscriberAndPlansOnPeriod(
                $subscriber->id,
                array_merge([$plan->id], $orderBumps),
                ['payments.status' => 'pending', 'payments.type_payment' => ['boleto', 'pix']],
                $platform->id,
                null,
                ['payments.id', 'payments.order_number']
            );

            if ($payments->count() > 0) {
                $ids = Arr::pluck($payments, 'id');
                $paymentRepository->batchUpdate(
                    $ids,
                    ['payments.status' => Payment::STATUS_EXPIRED]
                );
                $this->checkExpiredSubscriptions($payments, $platform);
            }
        } catch (Exception $e) {
            Log::error(
                'MundipaggCheckoutController@checkExpiredPayments > ',
                ['error' => $e->getMessage()]
            );
        }
    }

    private function expireOldPaymentsSameType($platform, $subscriber, $plan)
    {
        try {
            $payment = DB::table('payments')
                ->select([
                    'payments.id', 'payments.platform_id', 'payments.subscriber_id',
                    'payments.type_payment', 'payments.status', 'payments.created_at',
                    'payment_plan.plan_id'
                ])
                ->join('payment_plan', 'payments.id', '=', 'payment_plan.payment_id')
                ->where([
                    'payments.platform_id' => $platform->id,
                    'payments.subscriber_id' => $subscriber->id,
                    'payments.status' => Payment::STATUS_PENDING,
                    'payment_plan.plan_id' => $plan->id,
                ])
                ->orderBy('payments.created_at')
                ->get();

            if (!empty($payment) && count($payment) > 1) {
                DB::table('payments')
                    ->where('id', $payment[0]->id)
                    ->update(['payments.status' => Payment::STATUS_EXPIRED]);
            }
        } catch (Exception $e) {
            Log::error(
                'MundipaggCheckoutController@expireOldPaymentsSameType > ',
                ['error' => $e->getMessage()]
            );
        }
    }

    private function checkExpiredSubscriptions($payments, Platform $platform) {
        //Update all subscriptions of expired payments to cancalled
        $subscriptionRepository = app()->make(SubscriptionRepositoryInterface::class);
        $order_numbers = Arr::pluck($payments, 'order_number');
        $subscriptions = array();
        foreach ($order_numbers as $order_number) {
            $subscriptions = $subscriptionRepository->allByOrderNumber($order_number, ['*'], $platform->id);
        }
        $subscriptionsIds = Arr::pluck($subscriptions, 'id');
        $subscriptionRepository->updateById(
            $subscriptionsIds,
            [
                'status' => Subscription::STATUS_CANCELED,
                'status_updated_at' => now(),
                'canceled_at' => Carbon::now()
            ],
            $platform->id
        );
    }
}
