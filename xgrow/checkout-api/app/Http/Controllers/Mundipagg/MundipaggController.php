<?php

namespace App\Http\Controllers;

use App\Services\EmailService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Course;
use App\Order;
use stdClass;
use Config;
use App\Email;
use App\Integration;
use App\Mail\SendMailAuto;
use App\PlatformSiteConfig;
use DB;
use App\Platform;
use App\Subscription;
use Hash;
use App\Plan;
use App\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\Mundipagg\SubscriptionService as MundipaggSubscriptionService;
use App\Services\Mundipagg\ClientService as MundipaggClientService;
use App\Services\Finances\Customer\CardService as MundipaggCardService;
use App\Services\Mundipagg\OrderService as MundipaggOrderService;
use App\Constants;
use Illuminate\Support\Facades\Mail;

class MundipaggController extends Controller
{
    private $subscriber;
    private $plan;
    private $emailService;

    public function __construct()
    {
        $this->subscriber = new Subscriber;
        $this->plan = new Plan;
        $this->emailService = new EmailService();
    }

    public function register(Request $request, $platform_id, $plan_id, $course_id = false)
    {
        $plan = $this->plan->find(base64_decode($plan_id));

        if ($plan === null) {
            return view('mundipagg.error')->withErrors(['message' => "Houve um erro ao tentar abrir o plano escolhido! Entre em contato com o dono da plataforma!"]);
        }

        $platformSiteConfig = PlatformSiteConfig::where('platform_id', $platform_id)->first();
        $image_logo = $platformSiteConfig->image_logo_login->filename ?? '';
        $platform = Platform::find($platform_id);
        $platform_name = ($platform) ? $platform->name : config('app.name');

        return view('mundipagg.create', compact('platform_id', 'plan', 'course_id', 'platform_name', 'image_logo'));
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
        $subscriber->password = Hash::make($request->password);
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

        $courseUrl = "";
        if ($plan !== null && base64_decode($request->course_id) > 0) {
            if ($plan->type_plan === 'P') {
                $courseUrl = '/'.$request->course_id;
            }
        }

        return redirect('/mundipagg/'.$request->platform_id.'/'.base64_encode($request->plan_id).'/'.base64_encode($subscriber->id).'/c'.$courseUrl);
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

            return redirect('/mundipagg/'.$platform_id.'/'.$plan_id.'/c'.$courseUrl)->withErrors(['message' => 'Falha no cadastro, assinante não encontrado!']);
        }

        $name = explode(' ', $subscriber->name);
        $subscriber->first_name = array_shift($name);
        $subscriber->last_name = array_pop($name);
        $plan_id = base64_decode($plan_id);

        $plan = $this->plan->find($plan_id);
        $plan->recurrence_description = Plan::getDescription($plan->recurrence);
        $course_id = $course_id ?? 0;

        return view('mundipagg.register-card', compact('subscriber', 'platform_id', 'plan', 'subscriber', 'course_id'));
    }

    public function cardStore(Request $request)
    {
        $courseUrl = "";
        if (isset($request->course_id) && base64_decode($request->course_id) > 0) {
            $courseUrl = '/'.$request->course_id;
        }

        $paramsUrl = $request->platform_id.'/'.$request->plan_id.'/'.base64_encode($request->subscriber_id).'/c'.$courseUrl;

        $plan = $this->plan->find(base64_decode($request->plan_id));

        if($plan === null) {
            return redirect("/getnet/$paramsUrl")->withInput($request->except("password"))->withErrors(['message' => "Plano não encontrado!"]);
        }

        $integration = Integration::where('id', $plan->integration->integration_id)->first();

        if (verifyIntegration(Constants::CONSTANT_INTEGRATION_MUNDIPAGG, $request->platform_id) && isset($request->platform_id) &&
            $integration->id_webhook === Constants::getKeyIntegration(Constants::CONSTANT_INTEGRATION_MUNDIPAGG)) {

                $subscriber = Subscriber::find($request->subscriber_id);

                if ($subscriber === null) {
                    return back()->withInput($request->except("password"))->withErrors(['message' => "Assinante não encontrado em nosso banco de dados."]);
                }

                $mundipaggClientService = new MundipaggClientService($request->platform_id);
                $responseApi = $mundipaggClientService->store($subscriber);

                if ($responseApi['status'] !== 'success') {
                    $subscriber->delete();

                    $message = $responseApi['data']->message ?? "";

                    $search  = ["first_name", "last_name", "document_number", "customer_id", "document_type"];
                    $replace = ["Primeiro nome", "Sobrenome", "Número do documento", "cliente", "tipo de documento"];

                    $message = str_replace($search, $replace, $message);

                    $paramsUrl = $request->platform_id.'/'.$request->plan_id.'/c'.$courseUrl;

                    return redirect("/mundipagg/$paramsUrl")->withInput($request->except("password"))->withErrors(['message' => $message]);
                }


            $expiration = explode('/', $request->expiration);

            $formData = new stdClass;
            $formData->subscriberId = $subscriber->id;
            $formData->planId = $plan->id;
            $formData->subscriberName = $subscriber->name;
            $formData->subscriberEmail = $subscriber->email;
            $formData->number = $request->cardholder_identification;
            $formData->holderName = $request->cardholder_name;
            $formData->holderDocument = $subscriber->document_number;
            $formData->expMonth = $expiration[0];
            $formData->expYear = $expiration[1];
            $formData->cvv = $request->security_code;
            $formData->line1 = $subscriber->address_number.','.$subscriber->address_street.','.$subscriber->address_district;
            $formData->line2 = $subscriber->address_comp;
            $formData->zipCode = str_replace('-', '', $subscriber->address_zipcode);
            $formData->city = $subscriber->address_city;
            $formData->state = $subscriber->address_state;
            $formData->country = "BR";

            $mundipaggCardService = new MundipaggCardService($request->platform_id);

            $responseApi = $mundipaggCardService->store($formData);

            if ($responseApi['status'] !== 'success') {
                $arrayErrors[] = $responseApi['data']->message;

                if (isset($responseApi['data']->errors)) {
                    foreach ($responseApi['data']->errors as $key => $messages) {
                        foreach($messages as $message) {
                            $arrayErrors[] = $message;
                        }
                    }
                }

                return redirect("/mundipagg/$paramsUrl")->withErrors(['message' => $arrayErrors]);
            }

            $subscription = new Subscription;

            $subscription->platform_id = $request->platform_id;
            $subscription->plan_id = $plan->id;
            $subscription->subscriber_id = $subscriber->id;
            $subscription->save();

            $formData = new StdClass;
            $formData->code = $subscription->id;
            $formData->plan_id = $plan->integration->integration_type_id;
            $formData->payment_method = "credit_card";
            $formData->customer_id = $subscriber->integratable->where('integration_id', '=', $plan->integration->integration_id)->first()->integration_type_id;
            $formData->customer_name = $subscriber->name;

            $formData->card = new StdClass;
            $formData->card->number = $request->cardholder_identification;
            $formData->card->holderName = $request->cardholder_name;
            $formData->card->holderDocument = $subscriber->document_number;
            $formData->card->expMonth = $expiration[0];
            $formData->card->expYear = $expiration[1];
            $formData->card->cvv = $request->security_code;

            $formData->billinAddress = new StdClass;
            $formData->billinAddress->line1 = $subscriber->address_number.','.$subscriber->address_street.','.$subscriber->address_district;
            $formData->billinAddress->line2 = $subscriber->address_comp;
            $formData->billinAddress->zipCode = str_replace('-', '', $subscriber->address_zipcode);
            $formData->billinAddress->city = $subscriber->address_city;
            $formData->billinAddress->state = $subscriber->address_state;

            $mundipaggSubscriptionService = new MundipaggSubscriptionService($request->platform_id);
            $responseApi = $mundipaggSubscriptionService->store($formData);

            if ($responseApi['status'] !== 'success') {
                $arrayErrors[] = $responseApi['data']->message;

                if (isset($responseApi['data']->errors)) {
                    foreach ($responseApi['data']->errors as $key => $messages) {
                        foreach($messages as $message) {
                            $arrayErrors[] = $message;
                        }
                    }
                }

                return redirect("/mundipagg/$paramsUrl")->withErrors(['message' => $arrayErrors]);
            }

            $subscription = Subscription::find($subscription->id);

            if ($subscription === null) {
                $arrayErrors[] = "Assinatura não encontrada!";
                return redirect("/mundipagg/$paramsUrl")->withErrors(['message' => $arrayErrors]);
            }

            $subscription->integratable()->delete();
            $integration = Integration::where('platform_Id', $subscriber->platform_id)->where('id_integration', '=', Constants::CONSTANT_INTEGRATION_MUNDIPAGG)->first();
            $subscription->integratable()->create(['integration_id' => $integration->id, 'integration_type_id' => $responseApi['data']->id]);

            $subscriber = $this->subscriber->find($request->subscriber_id);
            $subscriber->status = Subscriber::STATUS_ACTIVE;
            $subscriber->save();

            // enviar e-mail de novo cadastro
            $this->emailService->sendMailNewRegisterSubscriber($subscriber);

            $messageSuccess = ($plan) ? $plan->message_success_checkout : 'Obrigado pelo pagamento!';

            return redirect("/mundipagg/thanks/$paramsUrl")->with(['message' => $messageSuccess]);
        }
    }

    public function thanks($platform_id, $plan_id, $subscriber_id)
    {
        $subscriber = $this->subscriber->find(base64_decode($subscriber_id));
        $plan = $this->plan->find(base64_decode($plan_id));
        return view('mundipagg.thanks', compact( 'subscriber', 'platform_id', 'plan'));
    }

    public function subscriptionCreate(Request $request)
    {

    }

    public function paymentCourse(Request $request, $platform_id, $course_id, $category)
    {

        if ($category !== Plan::ORDER_ITEM_CATEGORY_COURSE) {
            return redirect()->route('mundipagg.error', ['platform_id' => $platform_id, 'course_id' => 0]);
        }

        $courseId = base64_decode($course_id);
        if ($courseId <= 0) {
            return redirect()->route('mundipagg.error', ['platform_id' => $platform_id, 'course_id' => 0]);
        }

        $course = Course::find($courseId);

        if ($course === null) {
            return redirect()->route('mundipagg.error', ['platform_id' => $platform_id, 'course_id' => 0]);
        }

        $platform = Platform::where('id', $platform_id)->first();
        if ($platform === null) {
            return redirect()->route('mundipagg.error', ['platform_id' => $platform_id, 'course_id' => 0]);
        }

        $mundipaggOrderService = new MundipaggOrderService($request->platform_id);

        $responseApi = $mundipaggOrderService->store($course, $platform->url);

        if ($responseApi['data'] === "error") {
            return redirect()->route('mundipagg.error', ['platform_id' => $platform_id, 'course_id' => base64_encode($course->id)]);
        }

        return redirect($responseApi['data']);
    }

    public function errorUrlCheckout($platformId, $courseId)
    {

        $urlCheckout = config('app.url') . '/'. strtolower(Constants::CONSTANT_INTEGRATION_MUNDIPAGG). '/payment/' .$platformId. '/' . $courseId. '/course';

        if ($courseId > 0) {
            $message = "Houve um erro ao tentar abrir o checkout! <br>Tente novamente clicando <a href='".$urlCheckout."'>aqui</a>.";
        } else {
            $message = "Houve um erro ao tentar abrir o checkout! <br>Retorne à página anterior e clique no link novamente.";
        }

        return view('mundipagg.error')->withErrors(['message' => $message]);
    }
}
