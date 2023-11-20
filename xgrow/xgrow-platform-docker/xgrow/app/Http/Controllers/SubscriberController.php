<?php

namespace App\Http\Controllers;

use App\Constants;
use App\ContentSubscriber;
use App\CourseSubscriber;
use App\Email;
use App\EmailPlatform;
use App\File;
use App\Http\Controllers\Mundipagg\CreditCardController;
use App\Integration;
use App\Jobs\Imports\Subscribers\SubscriberImportQueue;
use App\Mail\SendMailAuto;
use App\Mail\SendMailChangeCard;
use App\Payment;
use App\PaymentCards;
use App\Plan;
use App\Repositories\Banks\Banks;
use App\Safe;
use App\Services\Contracts\JwtPlatformServiceInterface;
use App\Services\Contracts\SubscriberReportServiceInterface;
use App\Services\EmailService;
use App\Services\Getnet\ClientService as GetnetClientService;
use App\Services\LA\CacheClearService;
use App\Services\LAService;
use App\Services\Objects\SubscriberReportFilter;
use App\Subscriber;
use App\Subscription;
use DateTime;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use stdClass;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\JWTAuth;
use Yajra\Datatables\Datatables;
use App\Helpers\CollectionHelper;
use Illuminate\Http\Response;
use App\Http\Traits\CustomResponseTrait;
//use League\Csv\Statement;

// Credit Card

class SubscriberController extends Controller
{
    use CustomResponseTrait;

    private $subscriber;
    private $dataTable;
    private $subscription;
    private $plan;
    private $safe;
    private $paymentCard;
    private $course_subscribers;
    private $content_subscriber;
    private $emailService;
    private $subscriberReportService;
    private CacheClearService $cacheClearService;

    public function __construct(
        Subscriber $subscriber,
        Datatables $dataTable,
        Subscription $subscription,
        Plan $plan,
        Safe $safe,
        PaymentCards $paymentCard,
        CourseSubscriber $course_subscribers,
        ContentSubscriber $content_subscriber,
        EmailService $emailService,
        SubscriberReportServiceInterface $subscriberReportService,
        CacheClearService $cacheClearService
    )
    {
        $this->subscriber = $subscriber;
        $this->dataTable = $dataTable;
        $this->subscription = $subscription;
        $this->plan = $plan;
        $this->safe = $safe;
        $this->paymentCard = $paymentCard;
        $this->course_subscribers = $course_subscribers;
        $this->content_subscriber = $content_subscriber;
        $this->emailService = $emailService;
        $this->subscriberReportService = $subscriberReportService;
        $this->cacheClearService = $cacheClearService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        return response()->json(compact('token'));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAuthenticatedUser()
    {
        try {

            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());
        }

        return response()->json(compact('user'));
    }

    public function index(Request $request)
    {
        $plans = Plan::where('plans.platform_id', Auth::user()->platform_id)->get();

        $emailTemplate = EmailPlatform::select(['subject', 'message', 'from'])
            ->where('email_id', Email::CONSTANT_EMAIL_ACCESS_DATA)
            ->where('platform_id', Auth::user()->platform_id)
            ->first();

        if (!$emailTemplate) $emailTemplate = Email::select(['subject', 'message', 'from'])->find(Email::CONSTANT_EMAIL_ACCESS_DATA);


        return view('subscribers.index', ['plans' => $plans, 'emailTemplate' => $emailTemplate]);
    }

    public function indexNext(Request $request)
    {
        $plans = Plan::where('plans.platform_id', Auth::user()->platform_id)->get();

        $emailTemplate = EmailPlatform::select(['subject', 'message', 'from'])
            ->where('email_id', Email::CONSTANT_EMAIL_ACCESS_DATA)
            ->where('platform_id', Auth::user()->platform_id)
            ->first();

        if (!$emailTemplate) $emailTemplate = Email::select(['subject', 'message', 'from'])->find(Email::CONSTANT_EMAIL_ACCESS_DATA);


        return view('subscribers.indexNext', ['plans' => $plans, 'emailTemplate' => $emailTemplate]);
    }

    public function subscriberData()
    {
        try {
            $query = DB::select($this->subscriber->getSubscribers(Auth::user()->platform_id, Subscriber::STATUS_LEAD));
            return response()->json([
                'data' => $query,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);
        }
    }


    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $data = [];
        $data["plans"] = Plan::where('platform_id', Auth::user()->platform_id)->get();
        $data["genders"] = Subscriber::allGenders();
        $data["countrys"] = Subscriber::allCountrys();
        $data["states"] = Subscriber::allStates();
        $data["type"] = "create";
        $data['statusSubscription'] = "";
        $data['integration'] = "";
        $data['register_data'] = "";
        $data['address_country'] = 'BRA';

        return view('subscribers.create', $data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        try {
            $emailExists = Subscriber::where('email', $request->email)
                ->where('platform_id', Auth::user()->platform_id)
                ->where('status', 'active')
                ->get();

            if ($emailExists->count() > 0) {
                return back()->withInput($request->except("password"))->withErrors(['email_exists' => 'Este email já está cadastrado!']);
            }

            //Verify if password has minimum condition to create.
            passwordStrength($request->password);

            $subscriber = Subscriber::firstOrNew([
                'email' => $request->email,
                'platform_id' => Auth::user()->platform_id,
            ]);
            $subscriber->status = 'active';
            $subscriber->name = $request->name;
            $subscriber->email = $request->email;
            $subscriber->raw_password = $request->password;
            $subscriber->gender = $request->gender;
            $subscriber->birthday = ($request->birthday) ? DateTime::createFromFormat("d/m/Y", $request->birthday)->format('Y-m-d') : null;
            $subscriber->main_phone = $request->main_phone;
            $subscriber->cel_phone = $request->cel_phone;
            $subscriber->type = $request->type;
            $subscriber->tax_id_number = $request->document_number;
            $subscriber->document_type = "CPF";
            $subscriber->document_number = $request->document_number;
            $subscriber->source_register = Subscriber::SOURCE_PLATFORM;

            if ($request->type == 'legal_person') {
                $subscriber->document_type = "CNPJ";
                $company_data = [];
                $company_data["company_name"] = $request->company_name;
                $company_data["tax_id_br_ie"] = $request->tax_id_br_ie;
                $company_data["tax_id_br_im"] = $request->tax_id_br_im;
                $subscriber->company_data = json_encode($company_data);
            }

            $subscriber->address_zipcode = $request->address_zipcode;
            $subscriber->address_street = $request->address_street;
            $subscriber->address_number = $request->address_number;
            $subscriber->address_city = $request->address_city;
            $subscriber->address_country = $request->address_country;
            $subscriber->address_comp = $request->address_comp;
            $subscriber->address_state = $request->address_state;
            $subscriber->address_district = $request->address_district;
            $subscriber->platform_id = Auth::user()->platform_id;
            $subscriber->plan_id = $request->plan_id;

            $subscriber->save();

            $this->cacheClearService->clearSubscriberCache(Auth::user()->platform_id, $request->email, $subscriber->id);

            return redirect('/subscribers');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id, Request $request)
    {

        if (Gate::none(['lead', 'subscriber'])) {
            throw new AuthorizationException('Sem permissão de acesso');
        }

        $subscriber = Subscriber::findOrFail($id);

        if ($subscriber->platform_id != Auth::user()->platform_id) {
            return redirect('/subscribers');
        }

        $data = [];
        $subscriptions = Subscription::with([
            'plan' => function ($query) {
                $query->select(
                    'plans.id',
                    'plans.product_id',
                    'plans.name',
                    'plans.recurrence',
                    'plans.deleted_at'
                )
                    ->withTrashed();
            },
            'transaction' => function ($query) {
                $query->select(
                    'payments.id',
                    'payments.order_code',
                    'payments.order_number',
                    'payments.status',
                    'payments.type_payment',
                    'payments.type',
                    'payments.payment_date',
                    'payments.updated_at'
                );
            },
            'payments' => function ($query) {
                $query->select(
                    'payments.id',
                    'payments.order_code',
                    'payments.order_number',
                    'payments.status',
                    'payments.type_payment',
                    'payments.type',
                    'payments.payment_date',
                );
            },
            'subscriber' => function ($query) {
                $query->select(
                    'subscribers.id',
                    'subscribers.name',
                );
            }
        ])
            ->where('subscriptions.platform_id', Auth::user()->platform_id)
            ->where('subscriptions.subscriber_id', $id)
            ->get();

        $data['subscriptions'] = $subscriptions ?? [];

        if ($subscriber->type == 'legal_person') {
            $company_data = !empty($subscriber["company_data"]) ? json_decode($subscriber["company_data"], true) : '';

            $subscriber["company_name"] = ($company_data['company_name']) ?? '';
            $subscriber["tax_id_br_ie"] = ($company_data['tax_id_br_ie']) ?? '';
            $subscriber["tax_id_br_im"] = ($company_data['tax_id_br_im']) ?? '';
        }

        if ($subscriber->birthday)
            $subscriber->birthday = DateTime::createFromFormat('Y-m-d', $subscriber->birthday)->format("d/m/Y");

        $data["subscriber"] = $subscriber;
        $data["plans"] = Plan::where('platform_id', Auth::user()->platform_id)->get();
        $data["genders"] = Subscriber::allGenders();
        $data["countrys"] = Subscriber::allCountrys();
        $data["states"] = Subscriber::allStates();
        $data["type"] = "edit";

        $statusSubscription = $this->subscription->where('subscriber_id', $id)->orderBy('created_at', 'DESC')->first();

        $data['statusSubscription'] = "";

        if ($statusSubscription !== null && $statusSubscription->count() > 0) {
            if ($statusSubscription->payment_pendent !== null && $statusSubscription->payment_pendent !== '0000-00-00') {
                $data['statusSubscription'] = "Pagamento pendente";
            }
        }

        //        $integration = Integration::where('id_webhook', '=', Constants::getKeyIntegration('GETNET'))->first();
        $integrations = Integration::where('platform_id', Auth::user()->platform_id)->get();

        $test = [];
        $int = "";
        foreach ($integrations as $integration) {
            if (!in_array($integration->id, $test)) {
                $ret = $subscriber->integratable->where('integration_id', '=', $integration->id)->first();

                if ($ret !== null) {
                    $int .= "[" . $integration->name_integration . "]";
                }
            }
            array_push($test, $integration->id);
        }

        $data['integration'] = $int;
        $data['register_data'] = \Carbon\Carbon::parse($subscriber->created_at)->format('d/m/Y');

        // Get subscriber credit cards
        $creditCardController = new CreditCardController;
        $cards = $creditCardController->listCreditCards($subscriber->id)->getData();
        $data['cards'] = $cards;

        $data['bankList'] = Banks::getBankList();

        //Get payments data
        $data["payments"] = $this->getPayments($id);

        $data['tab'] = 'data';

        if($request->get('tab'))
            $data['tab'] = $request->get('tab');
        else
            $data['tab'] = 'data';

        return view('subscribers.edit', $data);
    }

    public function editNext($id, Request $request)
    {
        if (Gate::none(['lead', 'subscriber'])) {
            throw new AuthorizationException('Sem permissão de acesso');
        }

        return view('subscribers.indexNext', []);
    }


    public function getPayments($subscriberid)
    {
        //Getnet
        $payments = $this->subscription::join('integration_types', 'subscriptions.gateway_transaction_id', '=', 'integration_types.integration_type_id')
            ->join('getnet_charges', 'getnet_charges.subscription_id', '=', 'integration_types.integration_type_id')
            ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
            ->where('subscriptions.subscriber_id', '=', $subscriberid)
            ->select('getnet_charges.charge_id', 'getnet_charges.payment_date', 'getnet_charges.payment_type', 'plans.product_id', 'plans.name as plan_name', 'plans.price', 'getnet_charges.status')
            ->get();

        //Mundipagg
        if (count($payments) == 0) {
            $payments = Payment::
                        select(
                            'payments.*',
                            'payment_plan.plan_value',
                            'payment_plan.customer_value',
                            'payment_plan.id as payment_plan_id',
                            'payment_plan.status as status',
                            'plans.name as plan_name',
                            'plans.product_id',
                        )
                        ->join('payment_plan', 'payment_plan.payment_id', '=', 'payments.id')
                        ->join('plans', 'payment_plan.plan_id', '=', 'plans.id')
                        ->where('gateway', '=', 'mundipagg')
                        ->where('subscriber_id', '=', $subscriberid)
                        ->get();
        }

        return $payments ?? [];
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {
        try {
            if (Gate::none(['lead', 'subscriber'])) {
                throw new AuthorizationException('Sem permissão de acesso');
            }

            $subscriber = Subscriber::findOrFail($id);

            $initial_status = $subscriber->status;

            if ($subscriber->platform_id != Auth::user()->platform_id) {
                return redirect('/subscribers');
            }

            if ($subscriber->email != $request->email) { //alterando email
                $emailExists = Subscriber::where([
                    ['email', '=', $request->email],
                    ['id', '!=', $id],
                    ['platform_id', Auth::user()->platform_id]
                ])->first();

                if (!is_null($emailExists)) {
                    if($emailExists->status == 'lead'){
                        $emailExists->delete();
                    } else
                        return back()->withInput($request->except("password"))->withErrors(['email_exists' => 'Este email já está cadastrado!']);
                }

                Subscriber::where('id', $id)
                    ->update([
                        'email_bounce_at' => null,
                        'email_bounce_id' => null,
                        'email_bounce_type' => null,
                        'email_bounce_description' => null
                    ]);
            }

            $subscriber->name = $request->name;
            $subscriber->email = $request->email;

            if (!empty($request->password)) {
                passwordStrength($request->password);
                $subscriber->raw_password = $request->password;
            }

            $subscriber->gender = $request->gender;
            $subscriber->birthday = ($request->birthday) ? DateTime::createFromFormat("d/m/Y", $request->birthday)->format('Y-m-d') : null;
            $subscriber->main_phone = $request->main_phone;
            $subscriber->cel_phone = $request->cel_phone;
            $subscriber->type = $request->type;
            $subscriber->tax_id_number = $request->document_number;
            $subscriber->document_type = "CPF";
            $subscriber->document_number = $request->document_number;

            if ($request->type == 'legal_person') {
                $subscriber->document_type = "CNPJ";
                $company_data = [];
                $company_data["company_name"] = $request->company_name;
                $company_data["tax_id_br_ie"] = $request->tax_id_br_ie;
                $company_data["tax_id_br_im"] = $request->tax_id_br_im;

                $subscriber->company_data = json_encode($company_data);
            }

            $subscriber->address_zipcode = $request->address_zipcode;
            $subscriber->address_street = $request->address_street;
            $subscriber->address_number = $request->address_number;
            $subscriber->address_city = $request->address_city;
            $subscriber->address_country = $request->address_country;
            $subscriber->address_state = $request->address_state;
            $subscriber->address_district = $request->address_district;
            $subscriber->plan_id = $request->plan_id;
            $subscriber->address_comp = $request->address_comp;

            $subscriber->save();

            $this->cacheClearService->clearSubscriberCache(Auth::user()->platform_id, $request->email, $id);

            // return back()->with('success', 'Dados atualizados com sucesso!');
            if ($initial_status == 'lead')
                return redirect('/leads');
            else
                return redirect('/subscribers');
        } catch (Exception $e) {
            \Sentry\captureMessage($e->getMessage());
            return back()->withInput($request->except("password"))->withErrors(['generic' => 'Ops! Aconteceu algo errado, por favor entre em contato com a equipe de Suporte.']);
        }
    }

    /**
     * @deprecated Replaced by {@see \App\Http\Controllers\Subscribers\SubscriberListController::destroy()}
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        try {
            $subscriber = Subscriber::find($request->id);

            $this->safe->where('subscriber_id', $request->id)->delete();
            $this->paymentCard->where('subscriber_id', $request->id)->delete();
            $this->subscription->where('subscriber_id', $request->id)->delete();
            $this->course_subscribers->where('subscriber_id', $request->id)->delete();
            $this->content_subscriber->where('subscriber_id', $request->id)->delete();

            $this->cacheClearService->clearSubscriberCache(Auth::user()->platform_id, $subscriber->email, $subscriber->id);

            $subscriber->delete();

            return response()->json(['response' => 'success']);
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'SQLSTATE[23000]') === false)
                return response()->json(['response' => 'fail', 'message' => $e->getMessage()], 500);
            else
                return response()->json(['response' => 'fail', 'message' => 'possui comentários e/ou pagamentos registrados na plataforma'], 500);
        }
    }

    public function integrateAll()
    {
        $platform_id = Auth::user()->platform_id;

        if (verifyIntegration(Constants::CONSTANT_INTEGRATION_GETNET, $platform_id)) {
            $subscribers = Subscriber::where('subscribers.platform_id', '=', $platform_id)
                ->where('status', '=', 'active')
                ->where('document_type', '<>', NULL)
                ->where('document_type', '<>', '')
                ->where('document_number', '<>', NULL)
                ->where('document_number', '<>', '')
                ->get();

            if ($subscribers->count() > 0) {
                $getnetClientService = new GetnetClientService($platform_id);

                $cont = $errors = $success = 0;

                foreach ($subscribers as $subscriber) {

                    if (!isset($subscriber->integration->integratable_id)) {
                        $cont++;

                        $res = $getnetClientService->store($subscriber);

                        $response[] = $res;

                        ($res['status'] === 'error') ? $errors++ : $success++;
                    }
                }

                $response['total'] = $cont;
                $response['success'] = $success;
                $response['errors'] = $errors;
                dd($response);
            }
            dd('Fim sem registros');
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function seedUserInfo(Request $request)
    {
        try {

            $user = Subscriber::find(auth('api')->user()->id);

            $type_file_image = config('constants.type_file.image');
            $file = $request->file('icon');

            if (isset($file)) {
                $rules['icon'] = "mimes:{$type_file_image}";
                $validator = Validator::make($request->all(), $rules);
                $validator->validate();
                $user->thumb_id = File::saveFile($file)->id;
            }

            $filename = ($user->thumb == null) ? '' : $user->thumb->filename;

            $user->name = $request->name;

            if (isset($request->password)) {
                $user->raw_password = $request->password;
            }
            $user->gender = $request->gender;
            $user->birthday = $request->birthday;
            $user->main_phone = $request->main_phone;
            $user->cel_phone = $request->cel_phone;
            $user->type = $request->type;
            $user->tax_id_number = $request->tax_id_number;
            $user->document_type = "CPF";
            $user->document_number = $request->tax_id_number;

            if ($request->type == 'legal_person') {
                $user->document_type = "CNPJ";
                $company_data = [];
                $company_data["company_name"] = $request->company_name;
                $company_data["tax_id_br_ie"] = $request->tax_id_br_ie;
                $company_data["tax_id_br_im"] = $request->tax_id_br_im;

                $user->company_data = json_encode($company_data);
            }

            $user->address_zipcode = $request->address_zipcode;
            $user->address_street = $request->address_street;
            $user->address_number = $request->address_number;
            $user->address_city = $request->address_city;
            $user->address_state = $request->address_state;

            if (isset($request->external_id))
                $user->external_id = $request->external_id;

            $user->save();

            return response()->json(
                ['error' => false, 'message' => 'usuário atualizado', 'filename' => $filename]
            );
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function seedAcceptTerms(Request $request)
    {
        try {

            $user = Subscriber::find(auth('api')->user()->id);

            $user->accept_terms = 1;

            $user->save();

            return response()->json(
                ['error' => false, 'message' => 'success']
            );
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserInfo(Request $request)
    {
        try {

            $subscriber_id = (isset($request->subscriber_id)) ? $request->subscriber_id : auth('api')->user()->id;

            $user = $this->subscriber
                ->with('thumb:id,filename')
                ->find($subscriber_id);
            if ($user) {
                return response()->json([
                    'info' => $user,
                ]);
            }

            return response()->json(['error' => true, 'message' => 'Subscriber not found']);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function status($id, Request $request)
    {
        $status = array_keys(Subscriber::allStatus());
        if (!$request->exists('status') || !in_array($request->status, $status)) {
            return response()->json(['status' => 'error', 'message' => 'Status inválido.'], 400);
        }

        $subscriber = Subscriber::find($id);
        $subscriber->status = $request->status;
        $subscriber->save();

        return response()->json(['status' => $subscriber->status]);
    }

    /**
     * @param Request $request
     * @return string[]
     */
    public function passwordForgot(Request $request)
    {
        $user = Subscriber::where('email', $request->email)->first();

        if (!isset($user) || $user === null) {
            return ['status' => 'error', 'message' => 'E-mail não consta em nossa base de dados!'];
        }

        $pass = substr(sha1($user->email), 0, 6);

        $ret = $user->update([
            'raw_password' => $pass
        ]);

        $emailData = [
            'password' => $pass,
            'platform_id' => $user->platform_id,
            'user' => $user,
            'email_id' => Email::CONSTANT_EMAIL_FORGOT_PASSWORD
        ];

        $usersTo = [$user->email];

        $this->cacheClearService->clearSubscriberCache(Auth::user()->platform_id, $user->email, $user->id);

        EmailService::send($usersTo, new SendMailAuto($emailData));


        return ['status' => 'success', 'message' => 'Email enviado com sucesso! Verifique sua caixa de e-mail.'];
    }

    /**
     * @param $subscriber
     */
    public function sendMailNewRegisterSubscriber($subscriber)
    {
        $plan = $this->plan->find($subscriber->plan_id);

        if ($plan->trigger_email === 1) {

            $pass = keygen(12);

            $ret = $subscriber->update([
                'raw_password' => $pass
            ]);

            $emailData = [
                'subscriber' => true,
                'password' => $pass,
                'platform_id' => $subscriber->platform_id,
                'user' => $subscriber,
                'plan_name' => $plan->name,
                'email_id' => Email::CONSTANT_EMAIL_NEW_REGISTER,
            ];

            $usersTo = [$subscriber->email];

            $this->cacheClearService->clearSubscriberCache(Auth::user()->platform_id, $subscriber->email, $subscriber->id);

            EmailService::send($usersTo, new SendMailAuto($emailData));
        }
    }

    /**
     * @param $sub
     */
    public function mailTest($sub)
    {
        $subscriber = $this->subscriber->find($sub);
        if ($subscriber !== null) {

            $this->emailService->sendMailNewRegisterSubscriber($subscriber);
            echo 'ok';
        }
    }

    /**
     * @param $subscriber
     */
    public function storeSubscriberFandone($subscriber)

    {
        $curl = curl_init();

        $params = [
            "nome" => $subscriber->name,
            "email" => $subscriber->email,
            "senha" => "mJ72dnoA",
            "cpf" => $subscriber->document_number,
            "token" => "jdklf384jelf"
        ];

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://dnajj.com.br/alunos/c07d4635d13435cfd01f6125d2b056faa7955-ab6cc3b7a24b27eb9082af7455e81ef0b8d",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($params),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Cookie: __cfduid=defe2d9699625c4050e7dafa595c4e6101599679171"
            ),
        ));

        curl_exec($curl);

        //        $response = curl_exec($curl);
        //        curl_close($curl);
        //        echo $response;
    }

    /**
     * @deprecated Use {@see \App\Http\Controllers\Subscribers\SubscriberListController::resendData()} instead
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function resendData($id)
    {

        $subscriber = $this->subscriber->find($id);
        if ($subscriber === null) {
            return response()->json(['status' => 'error', 'message' => 'Assinante não foi encontrado!'], 404);
        }
        $subscriptions = $subscriber->subscriptions->first();

        if(empty($subscriptions)){
            return response()->json(['status' => 'error', 'message' => 'Para enviar os dados de acesso é necessário que o aluno possua pelo menos um produto cadastrado.'], 404);
        }

        $ret = $this->emailService->sendMailNewRegisterSubscriber($subscriber);
        if (!$ret) {
            return response()->json(['status' => 'error', 'message' => 'Plano desse assinante não está habilitado para envio de e-mail!', 'ret' => $ret], 404);
        }

        return response()->json(['status' => 'success', 'message' => "Dados enviados com sucesso!", 'ret' => $ret]);
    }

    public function resendDataToNonLogged()
    {

        $total = 0;

        $subscriber = $this->subscriber;

        //$subscriber = $subscriber->where('id', 52955);

        $subscriber = $subscriber->where('resend_data_to_non_logged', $total)
            ->where('platform_id', '029e0fac-d525-4796-8557-49bcf2b8fa68')
            ->where('status', 'active')->whereNull('login');


        $subscriber = $subscriber->first();

        if ($subscriber) {
            $ret = $this->emailService->sendMailNewRegisterSubscriber($subscriber, null, 1);
            $subscriber->resend_data_to_non_logged = $total + 1;
            $subscriber->save();
            $mensagem = "Email enviado para " . $subscriber->name . " " . $subscriber->email;
        } else {
            $mensagem = "Nenhum usuário localizado";
        }

        return view('subscribers.resend-data-to-non-logged', compact('mensagem'));
    }

    public function importCreate()
    {
        $subscriber = new Subscriber();
        $plans = Plan::where('platform_id', Auth::user()->platform_id)->get();
        return view('subscribers.import', compact('subscriber', 'plans'));
    }

    public function importCreateNext()
    {
        $subscriber = new Subscriber();
        $plans = Plan::where('platform_id', Auth::user()->platform_id)->get();
        return view('subscribers.importNext', compact('subscriber', 'plans'));
    }

    public function import(Request $request)
    {
        $rules['file'] = "required|mimes:csv,txt";
        $validator = Validator::make($request->all(), $rules);
        $validator->validate();

        if (!Auth::user()->platform_id) {
            return redirect('subscribers/import')->withErrors(['message' => 'Não foi encontrada sua plataforma, faça login novamente por favor!']);
        }

        if (!$request->file) {
            return redirect('subscribers/import')->withErrors(['message' => 'Selecione um arquivo para efetuar a importação.']);
        }

        $file = $request->file('file');

        $delimiter = $request->input(['delimiter']) ?? ';';

        $filename = 'subscriber_upload_' . $file->getFilename() . '.csv';

        $s3Filename = $filename;

        $sPath = $file->storeAs('uploads', $filename, 'images');

        $s3Path = Storage::disk('images')->url($sPath);

        $path = public_path('uploads/uploads/' . $filename);

        // $fileStorage = Storage::disk(env('STORAGE_DIR', 'images'))->putFileAs('uploads', $file->getPathname(), $filename);
        // $filePath = Storage::disk(env('STORAGE_DIR', 'images'))->url($fileStorage);

        $sendMail = $this->verifyPlanSendMail($request->plan_id);

        SubscriberImportQueue::dispatch(
            Auth::user(),
            $path,
            (int)$request->plan_id,
            $request->status,
            $sendMail,
            $this->emailService,
            $delimiter,
            $s3Path,
            $s3Filename
        );

        return redirect(route('subscribers.import.create'))->with('success', 'O seu arquivo de log pode ser visto em Listas exportadas no menu lateral Relatórios.');
    }

    public function exportCreate()
    {
        $filter_by_login = [
            0 => 'Todos',
            1 => 'Logados',
            2 => 'Não logados'
        ];
        return view('subscribers.export', compact('filter_by_login'));
    }

    public function export(Request $request)
    {
        if (!Auth::user()->platform_id) {
            return redirect('subscribers/export')->withErrors(['message' => 'Não foi encontrada sua plataforma, faça login novamente por favor!']);
        }

        if ($request->session()->get('code') != $request->input(['codeReceive'])) {
            return redirect('subscribers/export')->withErrors(['message' => 'Código de segurança inválido.']);
        }

        $fileName = 'subscribers.csv';
        $fields = $request->except(['_token', 'filter_by_login', 'codeReceive']);
        $filter = $request->only('filter_by_login');
        $platform_id = Auth::user()->platform_id;
        $sql = $this->subscriber->export(array_values($fields), $filter, $platform_id);
        $subscribers = DB::select($sql);

        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $columns = ['name', 'email', 'document_type', 'document_number', 'gender', 'address_zipcode', 'address_state', 'address_street', 'address_number', 'address_comp', 'status', 'login', 'plan', 'cel_phone'];

        $callback = function () use ($subscribers, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($subscribers as $subscriber) {
                $columns[0] = $subscriber->name;
                $columns[1] = $subscriber->email;
                $columns[2] = $subscriber->document_type;
                $columns[3] = $subscriber->document_number ?? null;
                $columns[4] = $subscriber->gender ?? null;
                $columns[5] = $subscriber->address_zipcode ?? null;
                $columns[6] = $subscriber->address_state ?? null;
                $columns[7] = $subscriber->address_street ?? null;
                $columns[8] = $subscriber->address_number ?? null;
                $columns[9] = $subscriber->address_comp ?? null;
                $columns[10] = $subscriber->status ?? null;
                $columns[11] = $subscriber->login ?? null;
                $columns[12] = $subscriber->plan ?? null;
                $columns[13] = $subscriber->cel_phone ?? null;

                fputcsv($file, array($columns[0], $columns[1], $columns[2], $columns[3], $columns[4], $columns[5], $columns[6], $columns[7], $columns[8], $columns[9], $columns[10], $columns[11], $columns[12], $columns[13]));
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function sendChangeCardLink($subscriber_id, JwtPlatformServiceInterface $jwtPlatformService)
    {
        try {
            $platformId = Auth::user()->platform_id;
            $subscriber = $this->subscriber->select(
                'subscribers.name',
                'subscribers.document_number',
                'subscribers.email',
                'subscribers.platform_id'
            )
                ->where('platform_id', $platformId)
                ->where('id', '=', $subscriber_id)
                ->firstOrFail();

            $token = $jwtPlatformService->generateToken($platformId, $subscriber->email, $subscriber->document_number ?? '');

            $baseUrl = env('APP_URL_SETTINGS', 'https://settings.xgrow.com');
            $urlWithToken = "$baseUrl/{$platformId}/dashboard?token={$token}";

            try {
                EmailService::mail([$subscriber->email], new SendMailChangeCard($platformId, $subscriber, $urlWithToken));
            } catch (Exception $e) {
                report($e);
                return response()->json(['error' => true, 'message' => 'Falha ao enviar email, favor entrar em contato com suporte'], 500);
            }

            return response()->json(array_merge(
                $subscriber->setAppends([])->toArray(),
                ['url' => $baseUrl]
            ));
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => true, 'message' => 'Aluno não encontrado'], 404);
        } catch (Exception $e) {
            report($e);
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }

    public function getChangeCardLink($subscriber_id, JwtPlatformServiceInterface $jwtPlatformService)
    {
        try {
            $platformId = Auth::user()->platform_id;
            $subscriber = $this->subscriber->select(
                'subscribers.name',
                'subscribers.document_number',
                'subscribers.email',
                'subscribers.platform_id'
            )
                ->where('platform_id', $platformId)
                ->where('id', '=', $subscriber_id)
                ->firstOrFail();

            $token = $jwtPlatformService->generateToken($platformId, $subscriber->email, $subscriber->document_number ?? '');

            $baseUrl = env('APP_URL_SETTINGS', 'https://settings.xgrow.com');
            $urlWithToken = "$baseUrl/{$platformId}/dashboard?token={$token}";

            return response()->json(['url' => $urlWithToken]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => true, 'message' => 'Aluno não encontrado'], 404);
        } catch (Exception $e) {
            report($e);
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }

    private function subscriberExist($platformId, $subscriberEmail)
    {
        $subscriber = Subscriber::wherePlatformId($platformId)->whereEmail($subscriberEmail)->first();
        if ($subscriber !== null) {
            return true;
        }
        return false;
    }

    private function verifyPlanSendMail($planId)
    {
        $plan = $this->plan->find($planId);
        if ($plan === null) {
            return false;
        }
        if ($plan->trigger_email === 0) {
            return false;
        }

        return true;
    }

    public function searchSubscriber(Request $request)
    {
        try {
            $createdPeriodFilter = ($request->input('createdPeriodFilter')) ? explode('-', $request->input('createdPeriodFilter')) : ['', ''];
            $lastAccessPeriodFilter = ($request->input('lastAccessPeriodFilter')) ? explode('-', $request->input('lastAccessPeriodFilter')) : ['', ''];
            $neverAccessedFilter = $request->input('neverAccessedFilter') === "true";
            $emailWrongFilter = $request->input('emailWrongFilter') === "true";

            $filters = new SubscriberReportFilter(
                $request->input('searchTermFilter'),
                $request->input('plansFilter'),
                $request->input('statusFilter'),
                parseBrDate($createdPeriodFilter[0]),
                parseBrDate($createdPeriodFilter[1]),
                parseBrDate($lastAccessPeriodFilter[0]),
                parseBrDate($lastAccessPeriodFilter[1]),
                $neverAccessedFilter,
                $emailWrongFilter,
            );

            $subscribers = $this->subscriberReportService->getSubscriberReport(
                Auth::user()->platform_id,
                $filters
            );

            return datatables()->of($subscribers)->toJson();
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()], 400);
        }
    }

    public function exportSubscriber(Request $request)
    {
        $searchTermFilter = $request->input('searchTermFilter') ?? null;
        $plansFilter = $request->input('plansFilter') ?? null;
        $statusFilter = $request->input('statusFilter') ?? null;
        $createdPeriodFilter = ($request->input('createdPeriodFilter')) ? explode('-', $request->input('createdPeriodFilter')) : ['', ''];
        $lastAccessPeriodFilter = ($request->input('lastAccessPeriodFilter')) ? explode('-', $request->input('lastAccessPeriodFilter')) : ['', ''];
        $neverAccessedFilter = $request->input('neverAccessedFilter') ?? null;
        $typeFile = $request->input('typeFile') ?? 'xlsx';
        $reportName = $request->input('reportName') ?? 'subscriber-users';

        $filters = new SubscriberReportFilter(
            $searchTermFilter,
            $plansFilter,
            $statusFilter,
            parseBrDate($createdPeriodFilter[0]),
            parseBrDate($createdPeriodFilter[1]),
            parseBrDate($lastAccessPeriodFilter[0]),
            parseBrDate($lastAccessPeriodFilter[1]),
            $neverAccessedFilter,
        );

        $this->subscriberReportService->exportReport(
            $reportName,
            $typeFile,
            Auth::user(),
            $filters
        );
    }

    public function searchBlockedSubscriber(Request $request)
    {
        $nameFilter = $request->input('nameFilter');

        $situationFilter = $request->input('situationFilter');
        $situationTypes = [
            'false' => 0, // not blocked
            'true' => 1, // is blocked
        ];

        $situation = $situationTypes[$situationFilter] ?? null; // null otherwiose

        try {
            $laApiService = new LAService(
                Auth::user()->platform_id,
                // '43d27ccc-d74e-4479-92dc-6d37b2b2aeb2',
                Auth::user()->id
            );

            $blockedSubscribers = (!empty(env('LA_PLATFORM_CONFIGURATION_API'))
                ? $laApiService->listBlockedAccesses()
                : new stdClass
            );

            if (property_exists($blockedSubscribers, 'data')) {
                $blockedSubscribers->data = array_map(function ($subs) {
                    $result = $this->subscriber->where('id', $subs->userId)->first();

                    $subs->userName = $result->name ?? '-';
                    $subs->userEmail = $result->email ?? '-';

                    return $subs;
                }, $blockedSubscribers->data);
            }

            $data = collect($blockedSubscribers->data ?? []);

            if ($nameFilter) {
                $data = $data->filter(function ($item) use ($nameFilter) {
                    return Str::contains(
                        Str::lower($item->userName),
                        Str::lower($nameFilter)
                    );
                });
            }

            if ($situation !== null) {
                $data = $data->filter(function ($item) use ($situation) {
                    return $item->isLocked == $situation;
                });
            }

            return datatables()->of($data)->toJson();
        } catch (Exception $e) {
            return response()->json([
                'error' => true,
                'response' => $e->getMessage()
            ], 400);
        }
    }

    public function updateBlockedSubscriber(Request $request)
    {
        try {
            $laApiService = new LAService(
                Auth::user()->platform_id,
                // '43d27ccc-d74e-4479-92dc-6d37b2b2aeb2',
                Auth::user()->id
            );

            $updated = $laApiService->updateBlockedAccess(
                $request->userId,
                $request->action
            );

            $this->cacheClearService->clearSubscriberCache(Auth::user()->platform_id, $request->userId);

            return response()->json($updated, 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => true,
                'response' => $e->getMessage()
            ], 400);
        }
    }
}
