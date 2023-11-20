<?php

namespace App\Http\Controllers;

use App\Services\EmailService;
use Config;
use App\Email;
use App\Integration;
use App\Mail\SendMailAuto;
use App\PlatformSiteConfig;
use DB;
use App\GetnetCharge;
use App\Platform;
use App\Services\GetnetService;
use App\Subscription;
use App\Plan;
use App\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Services\Getnet\ClientService as GetnetClientService;
use App\Services\Getnet\CardService as GetnetCardService;
use App\Constants;
use Illuminate\Support\Facades\Mail;

class GetnetController extends Controller
{
    private $subscriber;
    private $plan;
    private $api;
    private $token;
    private $emailService;

    public function __construct()
    {
        $this->subscriber = new Subscriber;
        $this->plan = new Plan;
        $this->emailService = new EmailService;

//        $this->middleware(['auth']);
//        $this->middleware(function ($request, $next) {
//            $user = Auth::user();
//            $this->getnetClientService = new GetnetClientService($user->platform_id);
//            return $next($request);
//        });
    }

    public function index()
    {
        return view('getnet.index');
    }

    public function clients()
    {
        $platform_id = Auth::user()->platform_id;

        return view('getnet.clients');

    }

    public function register(Request $request, $platform_id, $plan_id, $course_id = false)
    {
        $plan = $this->plan->find(base64_decode($plan_id));

        if ($plan === null) {
            return view('getnet.error')->withErrors(['message' => "Houve um erro ao tentar abrir o plano escolhido! Entre em contato com o dono da plataforma!"]);
        }

        $platformSiteConfig = PlatformSiteConfig::where('platform_id', $platform_id)->first();
        $image_logo = $platformSiteConfig->image_logo_login->filename ?? '';
        $platform = Platform::find($platform_id);
        $platform_name = ($platform) ? $platform->name : config('app.name');

        return view('getnet.create', compact('platform_id', 'plan', 'course_id', 'platform_name', 'image_logo'));
    }

    public function subscriberStore(Request $request)
    {
        $emailExists = Subscriber::where([
            ['email', $request->email],
            ['platform_id', $request->platform_id]
        ])->get();

        if ($emailExists->count() > 0) {
            $platform = Platform::find($request->platform_id);
            return redirect($platform->url);
        }

        $plan = $this->plan->find($request->plan_id);
        if ($plan->trigger_email === 1) {
            $request->request->add(['password' => rand()]);
        }

        $name = explode(" ", $request->name);
        $indexSurname = count($name) - 1;
        $surname = ($indexSurname == 0) ? "" :  $name[$indexSurname];
        if ($surname === "") {
            return back()->withInput($request->except("password"))->withErrors(['message' => 'Sobrenome é obrigatório']);
        }

        $subscriber = new Subscriber;
        $subscriber->name = $request->name;
        $subscriber->email = $request->email;
        $subscriber->raw_password = $request->password;
        $subscriber->main_phone = $request->main_phone;
        $subscriber->address_zipcode = str_replace('-', '', $request->address_zipcode);
        $subscriber->address_state = $request->address_state;
        $subscriber->address_city = $request->address_city;
        $subscriber->address_district = $request->address_district;
        $subscriber->address_street = $request->address_street;
        $subscriber->address_number = $request->address_number;
        $subscriber->address_comp = $request->address_comp;
        $subscriber->address_country = "Brasil";
        $subscriber->platform_id = $request->platform_id;
        $subscriber->plan_id = $request->plan_id;
        $subscriber->document_type = strtoupper($request->radioDocumentNumber);
        $subscriber->document_number = ($request->radioDocumentNumber === 'cpf') ? $request->cpf_number : $request->cnpj_number;
        $subscriber->status = Subscriber::STATUS_LEAD;
        $subscriber->cel_phone = $request->cel_phone;
        $subscriber->save();

        $plan = $this->plan->find($request->plan_id);
/*
        if (verifyIntegration('GETNET', $subscriber->platform_id) && isset($subscriber->platform_id)) {

            if($plan) {
                $integration = Integration::where('id_integration', '=', Constants::CONSTANT_INTEGRATION_GETNET)->where('platform_id', '=', $subscriber->platform_id)->first();
                if ($integration !== null && $integration->flag_enable === 1) {
                    $getnetClientService = new GetnetClientService($subscriber->platform_id);

                    $responseApi = $getnetClientService->store($subscriber);


                    if ($responseApi['status'] !== 'success') {
                        $subscriber->delete();

                        $message = $responseApi['data']->message ?? "";

                        $search  = ["first_name", "last_name", "document_number", "customer_id", "document_type"];
                        $replace = ["Primeiro nome", "Sobrenome", "Número do documento", "cliente", "tipo de documento"];

                        $message = str_replace($search, $replace, $message);

                        return back()->withInput($request->except("password"))->withErrors(['message' => $message]);
                    }
                }
            }
        }
*/
        $courseUrl = "";
        if ($plan !== null && base64_decode($request->course_id) > 0) {
            if ($plan->type_plan === 'P') {
                $courseUrl = '/'.$request->course_id;
            }
        }

        return redirect('/getnet/'.$request->platform_id.'/'.base64_encode($request->plan_id).'/'.base64_encode($subscriber->id).'/c'.$courseUrl);
    }

    public function cardRegister(Request $request, $platform_id, $plan_id, $subscriber_id, $course_id = 0)
    {
        $subscriber = $this->subscriber->find(base64_decode($subscriber_id));

        if ($subscriber === null) {

            $plan = $this->plan->find(base64_decode($plan_id));

            $courseUrl = "";
            if ($plan !== null && base64_decode($request->course_id) > 0) {
                if ($plan->type_plan === 'P') {
                    $courseUrl = '/'.$request->course_id;
                }
            }

            return redirect('/getnet/'.$platform_id.'/'.$plan_id.'/c'.$courseUrl)->withErrors(['message' => 'Falha no cadastro, assinante não encontrado!']);

        }

        $getnetApi = new GetnetService($platform_id);
        $seller_id = $getnetApi->getSellerId();

        $name = explode(' ', $subscriber->name);
        $subscriber->first_name = array_shift($name);
        $subscriber->last_name = array_pop($name);
        $plan_id = base64_decode($plan_id);

        $plan = $this->plan->find($plan_id);
        $plan->recurrence_description = Plan::getDescription($plan->recurrence);
        $urlCheckout = $getnetApi->getUrlCheckout();

        $course_id = $course_id ?? 0;

        return view('getnet.register-card', compact('seller_id', 'subscriber', 'platform_id', 'plan', 'urlCheckout', 'subscriber', 'course_id'));
    }

    public function cardStore(Request $request)
    {

        if (verifyIntegration('GETNET', $request->platform_id) && isset($request->platform_id)) {

            /////

            $courseUrl = "";
            if (isset($request->course_id) && base64_decode($request->course_id) > 0) {
                $courseUrl = '/'.$request->course_id;
            }

            $paramsUrl = $request->platform_id.'/'.$request->plan_id.'/'.base64_encode($request->subscriber_id).'/c'.$courseUrl;


            $plan = $this->plan->find(base64_decode($request->plan_id));

            if($plan === null) {
                return redirect("/getnet/$paramsUrl")->withInput($request->except("password"))->withErrors(['message' => "Plano não encontrado!"]);
            }

            $integration = Integration::where('id_integration', '=', Constants::CONSTANT_INTEGRATION_GETNET)->where('platform_id', '=', $request->platform_id)->first();
            if ($integration !== null && $integration->flag_enable === 1) {
                $getnetClientService = new GetnetClientService($request->platform_id);

                $subscriber = Subscriber::find($request->subscriber_id);

                if ($subscriber === null) {
                    return back()->withInput($request->except("password"))->withErrors(['message' => "Assinante não encontrado em nosso banco de dados."]);
                }

                $responseApi = $getnetClientService->store($subscriber);


                if ($responseApi['status'] !== 'success') {
                    $subscriber->delete();

                    $message = $responseApi['data']->message ?? "";

                    $search  = ["first_name", "last_name", "document_number", "customer_id", "document_type"];
                    $replace = ["Primeiro nome", "Sobrenome", "Número do documento", "cliente", "tipo de documento"];

                    $message = str_replace($search, $replace, $message);

//                        return back()->withInput($request->except("password"))->withErrors(['message' => $message]);

                    $paramsUrl = $request->platform_id.'/'.$request->plan_id.'/c'.$courseUrl;

                    return redirect("/getnet/$paramsUrl")->withInput($request->except("password"))->withErrors(['message' => $message]);
                }

            }

            /////

            $installment = $request->installment ?? 0;
            $course_id = base64_decode($request->course_id);

            $formData = [
                'cardholder_identification' => $request->cardholder_identification,
                'cardholder_name' => $request->cardholder_name,
                'expiration' => $request->expiration,
                'security_code' => $request->security_code,
                'platform_id' => $request->platform_id,
                'plan_id' => $request->plan_id,
                'subscriber_id' => $request->subscriber_id,
                'installment' => $installment,
                'course_id' => $course_id
            ];

            $getnetCardService = new GetnetCardService($request->platform_id);

            $responseApi = $getnetCardService->store($formData);

//            $arrayResponse = json_decode($responseApi->content(), true);

//            $courseUrl = "";
//            if (isset($request->course_id) && base64_decode($request->course_id) > 0) {
//                $courseUrl = '/'.$request->course_id;
//            }
//
//            $paramsUrl = $request->platform_id.'/'.$request->plan_id.'/'.base64_encode($request->subscriber_id).'/c'.$courseUrl;

            if ($responseApi['status'] !== 'success') {
                $textMessages = $responseApi['data']->message.'\r\n';
                $arrayErrors[] = $responseApi['data']->message;
                if (isset($responseApi['data']->details) && count($responseApi['data']->details) > 0) {
                    foreach ($responseApi['data']->details as $messages) {
                        if (isset($messages['description']) && isset($messages['description_detail'])) {
                            $textMessages.= $messages['description'] . '[' . $messages['description_detail'] . ']' . '\r\n';
                            $arrayErrors[] = $messages['description'] . '[' . $messages['description_detail'] . ']';
                        }

						/*
						if (isset($messages->description) && isset($messages->description_detail)) {
                            $textMessages.= $messages->description . '[' . $messages->description_detail . ']' . '\r\n';
                            $arrayErrors[] = $messages->description . '[' . $messages->description_detail . ']';
                        }
						*/
                    }
                }

                return redirect("/getnet/$paramsUrl")->withErrors(['message' => $arrayErrors]);
            }

            // enviar e-mail de novo cadastro
            $subscriber = $this->subscriber->find($request->subscriber_id);
            $subscriber->status = Subscriber::STATUS_ACTIVE;
            $subscriber->save();

            $this->emailService->sendMailNewRegisterSubscriber($subscriber);

            //$plan = $this->plan->find(base64_decode($request->plan_id));
            $messageSuccess = ($plan) ? $plan->message_success_checkout : 'Obrigado pelo pagamento!';



            return redirect("/getnet/thanks/$paramsUrl")
                ->with([
                    'message' => $responseApi['message'],
                    'message_thanks' => $messageSuccess
                ]);

        }
    }

    // inicio checkout da GetNet ========================== não apagar até decidir como irá ficar

//    public function checkout($platform_id, $plan_id, $subscriber_id)
//    {
//        $authString = base64_encode(Constants::getClientId().":".Constants::getSecretId());
//        $subscriber_id = base64_decode($subscriber_id);
//
//        $client = new \GuzzleHttp\Client();
//
//        $url = Constants::getUrlApi();
//
//        $apiRequest = $client->request('POST', $url.'/auth/oauth/v2/token', [
//            'headers' => [
//                'Content-Type' => 'application/x-www-form-urlencoded',
//                'Authorization' => 'Basic '.$authString
//            ],
//            'form_params' => [
//                'scope' => 'oob',
//                'grant_type' => 'client_credentials'
//            ]
//        ]);
//
//        $response = json_decode($apiRequest->getBody());
//
//        $subscriber = $this->subscriber->find($subscriber_id);
//
//        $seller_id = Constants::getSellerId();
//
//        $name = explode(' ', $subscriber->name);
//        $subscriber->first_name = array_shift($name);
//        $subscriber->last_name = array_pop($name);
//        $plan_id = base64_decode($plan_id);
//
//        $plan = $this->plan->find($plan_id);
//        $plan->recurrence_description = Plan::getDescription($plan->recurrence);
//        $urlCheckout = Constants::getUrlCheckout();
//
//        return view('getnet.checkout', compact('response','seller_id', 'subscriber', 'platform_id', 'plan', 'urlCheckout'));
//    }
    // fim checkout GetNet ==========================

    public function thanks($platform_id, $plan_id, $subscriber_id)
    {
        $getnetApi = new GetnetService($platform_id);
        $seller_id = $getnetApi->getSellerId();

        $subscriber = $this->subscriber->find(base64_decode($subscriber_id));

        $plan = $this->plan->find(base64_decode($plan_id));

        return view('getnet.thanks', compact('seller_id', 'subscriber', 'platform_id', 'plan'));
    }

    public function charges()
    {
        $platforms = Platform::join('integrations', 'integrations.platform_id', '=', 'platforms.id')
            ->where('platforms.deleted_at', null)
            ->where('integrations.id_webhook', 4)
            ->select('platforms.id AS platform_id', 'integrations.id_webhook')
            ->get();

        if ($platforms->count() > 0 ) {

            foreach($platforms as $platform) {

                $params = ['page' => 1, 'limit' => 500];
                $callback = $this->importCharges($platform->platform_id, $params);

                if(count($callback->charges) > 0) {
                    $this->recordCharges($callback->charges);
                }

                $calls = (int) ceil($callback->total / 500);

                if($calls > 1) {
                    for ($i = 2; $i <= $calls; $i++) {
                        $params = ['page' => $i, 'limit' => 500];
                        $callback = $this->importCharges($platform->platform_id, $params);

                        if(count($callback->charges) > 0) {
                            $this->recordCharges($callback->charges);
                        }
                    }
                }
            }
        }
    }

    public function importCharges($platformId, $params)
    {
        $getnetService = new GetnetService($platformId);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $getnetService->getUrlApi() . "/v1/charges?page=" . $params['page'] . "&limit=" . $params['limit'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/x-www-form-urlencoded",
                "Authorization: Bearer " . $getnetService->getToken(),
                "seller_id: " . $getnetService->getSellerId()
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response, false);
    }

    public function recordCharges($charges)
    {
        foreach($charges as $charge){

            $data["amount"] = substr($charge->amount, 0, -2) . '.' . substr($charge->amount, -2) ;
            $data["charge_id"] = $charge->charge_id;
            $data["seller_id"] = $charge->seller_id;
            $data["subscription_id"] = $charge->subscription_id;
            $data["customer_id"] = $charge->customer_id;
            $data["plan_id"] = $charge->plan_id;
            $data["payment_id"] = $charge->payment_id;
            $data["status"] = $charge->status;
            $data["scheduled_date"] = $charge->scheduled_date;
            $data["create_date"] = $charge->create_date;
            $data["retry_number"] = $charge->retry_number;
            $data["payment_date"] = $charge->payment_date;
            $data["payment_type"] = $charge->payment_type;
            $data["terminal_nsu"] = $charge->terminal_nsu;
            $data["authorization_code"] = $charge->authorization_code;
            $data["acquirer_transaction_id"] = $charge->acquirer_transaction_id;
            $data["installment"] = $charge->installment;

            $insert = DB::table('getnet_charges')->insertOrIgnore($data); // funciona
            if ($insert === 0){
                $update = DB::table('getnet_charges')->where('charge_id', $data['charge_id']);
                $update->update($data);
            }
        }

    }

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
                'email_id' => Email::CONSTANT_EMAIL_NEW_REGISTER
            ];

            $usersTo = [$subscriber->email];

            EmailService::send($usersTo, new SendMailAuto($emailData));

            $config = ['name' => 'Fandone'];

            Config::set('app', $config);
        }
    }
}
