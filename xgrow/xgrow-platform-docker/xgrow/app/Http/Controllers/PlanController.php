<?php

namespace App\Http\Controllers;

use App\Course;
use App\CoursePlan;
use App\File;
use App\Helpers\SecurityHelper;
use App\Plan;
use App\PlatformUser;
use App\Section;
use App\SectionPlan;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use stdClass;
use App\Email;
use App\Constants;
use App\Subscriber;
use App\Integration;
use App\PlanCategory;
use App\EmailPlatform;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\Getnet\PlanService as GetnetPlanService;
use App\Services\Mundipagg\PlanService as MundipaggPlanService;

class PlanController extends Controller
{
    const URL_REGEX = "/^((?:https?\:\/\/|www\.)(?:[-a-z0-9]+\.)*[-a-z0-9]+.*)$/";
    private $getnetPlanService;
    private $mundipaggPlanService;

    public function __construct()
    {

        $this->middleware(['auth']);
        $this->middleware(function ($request, $next) {
            $user = Auth::user();

            if (verifyIntegration(Constants::CONSTANT_INTEGRATION_GETNET, $user->platform_id)) {
                $this->getnetPlanService = new GetnetPlanService($user->platform_id);
            }

            if (verifyIntegration(Constants::CONSTANT_INTEGRATION_MUNDIPAGG, $user->platform_id)) {
                $this->mundipaggPlanService = new MundipaggPlanService($user->platform_id);
            }

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $data = [];
        $query = Plan::with([
            'integratable.integration' => function ($query) {
                $query->select('id', 'id_webhook', 'id_integration');
            }
        ])
            ->where('platform_id', Auth::user()->platform_id);

        if ($request->ajax()) {
            return datatables()->eloquent($query)->make();
        }

        $data["totalLabel"] = getTotalLabel($query, 'produto');
        return view('plans.index', $data);
    }

    public function list(Request $request)
    {
        try {

            $plans = Plan::select('id', 'name')->where('platform_id', Auth::user()->platform_id)->get();

            return response()->json(['status' => 'success', 'plans' => $plans]);
        } catch (Exception $e) {
            return response()->json(['status' => 'success', 'data' => $e, 'message' => $e->getMessage()], 500);
        }
    }

    public function create()
    {
        $data = [];
        $plan = new Plan;
        $recurrence = Plan::allRecurrences();
        $freedays_type = Plan::allFreeDaysType();
        $currency = Plan::allCurrencys();
        $gateways = Integration::where('platform_id', '=', Auth::user()->platform_id)->where('flag_enable', 1)->get();
        $categories = PlanCategory::select('id', 'name')
            ->orderBy('name', 'asc')
            ->get();

        $image = new stdClass;
        $image->id = 0;

        return view('plans.create', compact('recurrence', 'freedays_type', 'currency', 'gateways', 'image', 'plan', 'categories'));
    }

    public function store(Request $request)
    {
        if (!$request->has('installment')) {
            $request->request->add(['installment' => 1]);
        }

        $rules = [
            "name" => "required",
            "currency" => "required",
            "price" => "required",
            "type_plan" => "required",
            "checkout_layout" => "required",
            "email" => "nullable|email",
            "message_success_checkout" => "required_without:url_checkout_confirm",
            "url_checkout_confirm" => ["nullable", "regex:" . self::URL_REGEX],
            "checkout_url_terms" => ["nullable", "regex:" . self::URL_REGEX],
            "installment" => [
                function ($attribute, $value, $fail) use ($request) {
                    $price = str_replace(',', '.', str_replace('.', '', $request->price));
                    if ($request->type_plan === 'P') {
                        $maxInstallment = intval($price / 5);
                        if ($maxInstallment < ($value ?? 12)) {
                            $fail('O número máximo de parcelas é inválido. O valor mínimo da parcela deve ser de R$5,00.');
                        }
                    }
                }
            ]
        ];

        if (!($request->payment_method_boleto ||
            $request->payment_method_credit_card ||
            $request->payment_method_pix ||
            $request->payment_method_multiple_cards ||
            $request->unlimited_sale
        )) {
            $rules['form_payment'] = "required";
        }

        $image = File::setUploadedFile($request, 'image');

        $plan = new Plan;

        $plan->status = isset($request->status) ?? 0;
        $plan->name = $request->name;
        $plan->recurrence = $request->recurrence = $request->recurrence ?? 1;
        $plan->charge_until = $request->charge_until;
        $plan->currency = $request->currency ?? 'BRL';
        $plan->price = str_replace(',', '.', str_replace('.', '', $request->price));
        $plan->setup_price = str_replace(',', '.', str_replace('.', '', $request->setup_price));
        $plan->freedays_type = $request->freedays_type;
        $plan->freedays = $request->freedays;
        $plan->platform_id = \Illuminate\Support\Facades\Auth::user()->platform_id;
        $plan->type_plan = $request->type_plan;
        $plan->installment = ($request->type_plan === 'P') ? $request->installment : 1;
        $plan->trigger_email = $request->trigger_email ?? 0;
        $plan->description = $request->description;
        $plan->message_success_checkout = $request->message_success_checkout;
        $plan->url_checkout_confirm = isset($request->url_checkout_confirm) ? ((strpos($request->url_checkout_confirm, 'https://') !== false) || (strpos($request->url_checkout_confirm, 'http://') !== false) ? $request->url_checkout_confirm : 'https://' . $request->url_checkout_confirm) : null;
        $plan->order_bump_plan_id = $request->order_bump_plan_id;
        $plan->upsell_plan_id = $request->upsell_plan_id;
        $plan->unlimited_sale = $request->unlimited_sale ?? 0;
        $plan->use_promotional_price = $request->use_promotional_price ?? false;
        $plan->promotional_price = ($request->promotional_price) ? str_replace(',', '.', str_replace('.', '', $request->promotional_price)) : null;
        $plan->promotional_periods = $request->promotional_periods;
        $plan->payment_method_credit_card = ($request->type_plan === 'R') ? true : ($request->payment_method_credit_card ?? false);
        $plan->payment_method_boleto = $request->payment_method_boleto ?? false;
        $plan->payment_method_pix = $request->payment_method_pix ?? false;
        $plan->payment_method_multiple_cards = $request->payment_method_multiple_cards ?? false;
        $plan->order_bump_discount = $request->order_bump_discount;
        $plan->order_bump_message = $request->order_bump_message;
        $plan->upsell_discount = $request->upsell_discount;
        $plan->upsell_message = $request->upsell_message;
        $plan->upsell_video_url = $request->upsell_video_url;
        $plan->checkout_whatsapp = $request->checkout_whatsapp;
        $plan->checkout_email = $request->checkout_email;
        $plan->checkout_support = $request->checkout_support;
        $plan->checkout_facebook_pixel = $request->checkout_facebook_pixel;
        $plan->checkout_google_tag = $request->checkout_google_tag;
        $plan->checkout_url_terms = $request->checkout_url_terms;
        $plan->checkout_support_platform = $request->checkout_support_platform;
        $plan->checkout_layout = $request->checkout_layout;
        $plan->checkout_address = $request->checkout_address ?? false;
        $plan->category_id = $request->category_id;

        $plan->save();

        if (!$image->id == 0) {
            File::saveUploadedFile($plan, $image, 'image_id');
        } else {
            $plan->image_id = 0;
            $plan->save();
        }

        $hasOrderBumpImage = $request->order_bump_image_upimage_url ? true : false;
        if ($hasOrderBumpImage) {
            $orderBumpImage = File::setUploadedFile($request, 'order_bump_image');
            File::saveUploadedFile($plan, $orderBumpImage, 'order_bump_image_id');
        }

        $hasUpsellImage = $request->upsell_image_upimage_url ? true : false;
        if ($hasUpsellImage) {
            $upsellImage = File::setUploadedFile($request, 'upsell_image');
            File::saveUploadedFile($plan, $upsellImage, 'upsell_image_id');
        }

        $this->storePlanGetnet($request, $plan);

        //$this->storePlanMundipagg($plan);
        //saves integration while plan synchronization mundipagg disabled
        $plan->integratable()->delete();
        if (strlen($request->integration_id) > 0) {
            $plan->integratable()->create(['integration_id' => $request->integration_id, 'integration_type_id' => $request->integration_id]);
        }
        //        return redirect("/plans/$plan->id/edit/?delivery=true");
        return redirect("/plans/$plan->id/edit");
    }

    public function edit($id)
    {
        try {
            $data = [];
            $plan = Plan::find($id);
            (new SecurityHelper)->securityUser($plan);
            $data["plan"] = $plan;
            $data["recurrence"] = Plan::allRecurrences();
            $data["freedays_type"] = Plan::allFreeDaysType();
            $data["currency"] = Plan::allCurrencys();
            $data["gateways"] = Integration::where('platform_id', '=', Auth::user()->platform_id)->where('flag_enable', 1)->get();
            $data["urlCheckout"] = "";
            $data["showDivCustom"] = false;
            $data['categories'] = PlanCategory::select('id', 'name')
                ->orderBy('name', 'asc')
                ->get();
            if (isset($plan->integration->integration_id)) {
                $integration = Integration::where('id', $plan->integration->integration_id)->first();
                if ($integration->id_integration == Constants::CONSTANT_INTEGRATION_MUNDIPAGG) {
                    $data["urlCheckout"] = $urlCheckout = config('app.url_checkout') . '/' . Auth::user()->platform_id . '/' . base64_encode($plan->id);
                } else {
                    $data["urlCheckout"] = config('app.url') . '/' . strtolower($integration->id_integration) . '/' . Auth::user()->platform_id . '/' . base64_encode($plan->id) . '/c';
                }
                $data["showDivCustom"] = in_array($integration->id_webhook, array(
                    Constants::getKeyIntegration(Constants::CONSTANT_INTEGRATION_GETNET),
                    Constants::getKeyIntegration(Constants::CONSTANT_INTEGRATION_MUNDIPAGG)
                ));
            }
            $image = new stdClass;
            $image->id = 0;
            $data["image"] = $image;
            // Recovery all courses and sections
            $data["courses"] = Course::where('platform_id', Auth::user()->platform_id)->get();
            $data["sections"] = Section::where('platform_id', Auth::user()->platform_id)->get();
            // Get all existing restrictions
            $data["coursePlan"] = CoursePlan::where('plan_id', $plan->id)->get();
            $data["sectionPlan"] = SectionPlan::where('plan_id', $plan->id)->get();
            $data["coursePlanPluck"] = CoursePlan::where('plan_id', $plan->id)->pluck('course_id')->toArray();
            $data["sectionPlanPluck"] = SectionPlan::where('plan_id', $plan->id)->pluck('section_id')->toArray();
            $data["planDelivery"] = Plan::DELIVERY_PLANS;
            $data["hasDelivery"] = (CoursePlan::where('plan_id', $plan->id)->count() > 0 || SectionPlan::where('plan_id', $plan->id)->count() > 0);
            return view('plans.create', $data);
        } catch (Exception $e) {
            return redirect()->route('plans.index')->with('error', $e->getMessage());
        }
    }

    public function restrictions(Request $request, $plan_id)
    {
        if ($request->planDelivery == 'limited') {
            $course_ids = $request->input('course_ids') ?? null;
            $section_ids = $request->input('section_ids') ?? null;

            if ($course_ids && count($course_ids) > 0) {
                foreach ($course_ids as $course) {
                    $row = CoursePlan::where(['course_id' => $course, 'plan_id' => $plan_id])->get();
                    if (count($row) == 0) {
                        CoursePlan::insert([
                            'course_id' => $course,
                            'plan_id' => $plan_id,
                        ]);
                    }
                }
            }

            if ($section_ids && count($section_ids) > 0) {
                foreach ($section_ids as $section) {
                    $row = SectionPlan::where(['section_id' => $section, 'plan_id' => $plan_id])->get();
                    if (count($row) == 0) {
                        SectionPlan::insert([
                            'section_id' => $section,
                            'plan_id' => $plan_id,
                        ]);
                    }
                }
            }
        } else {
            CoursePlan::where('plan_id', $plan_id)->delete();
            SectionPlan::where('plan_id', $plan_id)->delete();
        }
        //        return redirect("/plans/$plan_id/edit/?delivery=true");
        return redirect("/plans/$plan_id/edit");
    }

    public function restrictionsDelete(Request $request)
    {
        try {
            $id = 0;
            if ($request->input('type') == 'course') {
                $course = CoursePlan::where(['id' => $request->input('id')])->first();
                $id = $course->course_id;
                $course->delete();
            }
            if ($request->input('type') == 'section') {
                $section = SectionPlan::where(['id' => $request->input('id')])->first();
                $id = $section->section_id;
                $section->delete();
            }
            return response()->json(['status' => 'success', 'message' => 'Item removido com sucesso.', 'id' => $id]);
        } catch (Exception $e) {
            return response()->json(['status' => 'success', 'data' => $e, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $rules = [
                "name" => "required",
                "currency" => "required",
                "price" => "required",
                "type_plan" => "required",
                "checkout_layout" => "required",
                "email" => "nullable|email",
                "message_success_checkout" => "required_without:url_checkout_confirm",
                "url_checkout_confirm" => ["nullable", "regex:" . self::URL_REGEX],
                "checkout_url_terms" => ["nullable", "regex:" . self::URL_REGEX],
                "installment" => [
                    function ($attribute, $value, $fail) use ($request) {
                        $price = str_replace(',', '.', str_replace('.', '', $request->price));
                        if ($request->type_plan === 'P') {
                            $maxInstallment = intval($price / 5);
                            if ($maxInstallment < ($value ?? 12)) {
                                $fail('O número máximo de parcelas é inválido. O valor mínimo da parcela deve ser de R$5,00.');
                            }
                        }
                    }
                ]
            ];

            if (!($request->payment_method_boleto ||
                $request->payment_method_credit_card ||
                $request->payment_method_pix ||
                $request->payment_method_multiple_cards ||
                $request->unlimited_sale
            )) {
                $rules['form_payment'] = "required";
            }

            $plan = Plan::find($id);

            (new SecurityHelper)->securityUser($plan);

            $image = File::setUploadedFile($request, 'image');

            // $request->price = str_replace('.', ',', $request->price);

            $updateStatusPlanGetnet = $updatePlanGetnet = $createPlanGetnet = false;
            $updatePlanMundipagg = $createPlanMundipagg = false;

            if (($plan->recurrence <> $request->recurrence) ||
                ($plan->price <> str_replace(',', '.', $request->price))
            ) {
                $createPlanMundipagg = $createPlanGetnet = true;
            }

            if ($plan->currency <> $request->currency) {
                $updatePlanMundipagg = $createPlanGetnet = true;
            }

            if ($plan->status <> $request->status) {
                $updatePlanMundipagg = $updateStatusPlanGetnet = true;
            }

            if ($plan->name <> $request->name) {
                $updatePlanMundipagg = $updatePlanGetnet = true;
            }

            $plan->status = isset($request->status) ?? 0;
            $plan->name = $request->name;
            $plan->recurrence = $request->recurrence = $request->recurrence ?? 1;
            $plan->charge_until = $request->charge_until;
            $plan->currency = $request->currency ?? 'BRL';
            $plan->price = str_replace(',', '.', str_replace('.', '', $request->price));
            $plan->setup_price = str_replace(',', '.', str_replace('.', '', $request->setup_price));
            $plan->freedays_type = $request->freedays_type;
            $plan->freedays = $request->freedays;
            $plan->type_plan = $request->type_plan;
            $plan->installment = ($request->type_plan === 'P') ? (isset($request->installment) ? $request->installment : $plan->installment) : 1;
            $plan->trigger_email = $request->trigger_email ?? 0;
            $plan->description = $request->description;
            $plan->message_success_checkout = $request->message_success_checkout;
            $plan->url_checkout_confirm = isset($request->url_checkout_confirm) ? ((strpos($request->url_checkout_confirm, 'https://') !== false) || (strpos($request->url_checkout_confirm, 'http://') !== false) ? $request->url_checkout_confirm : 'https://' . $request->url_checkout_confirm) : null;
            $plan->order_bump_plan_id = $request->order_bump_plan_id;
            $plan->upsell_plan_id = $request->upsell_plan_id;
            $plan->unlimited_sale = $request->unlimited_sale ?? 0;
            $plan->use_promotional_price = $request->use_promotional_price ?? false;
            $plan->promotional_price = ($request->promotional_price) ? str_replace(',', '.', str_replace('.', '', $request->promotional_price)) : null;
            $plan->promotional_periods = $request->promotional_periods;
            $plan->payment_method_credit_card = ($request->type_plan === 'R') ? true : ($request->payment_method_credit_card ?? false);
            $plan->payment_method_boleto = $request->payment_method_boleto ?? false;
            $plan->payment_method_pix = $request->payment_method_pix ?? false;
            $plan->payment_method_multiple_cards = $request->payment_method_multiple_cards ?? false;
            $plan->order_bump_discount = $request->order_bump_discount;
            $plan->order_bump_message = $request->order_bump_message;
            $plan->upsell_discount = $request->upsell_discount;
            $plan->upsell_message = $request->upsell_message;
            $plan->upsell_video_url = $request->upsell_video_url;
            $plan->checkout_whatsapp = $request->checkout_whatsapp;
            $plan->checkout_email = $request->checkout_email;
            $plan->checkout_support = $request->checkout_support;
            $plan->checkout_facebook_pixel = $request->checkout_facebook_pixel;
            $plan->checkout_google_tag = $request->checkout_google_tag;
            $plan->checkout_url_terms = $request->checkout_url_terms;
            $plan->checkout_support_platform = $request->checkout_support_platform;
            $plan->checkout_layout = $request->checkout_layout;
            $plan->checkout_address = $request->checkout_address ?? false;
            $plan->category_id = $request->category_id;

            $plan->save();

            if (!$image->id == 0) {
                File::saveUploadedFile($plan, $image, 'image_id');
            } else {
                $plan->image_id = 0;
                $plan->save();
            }

            $hasOrderBumpImage = $request->order_bump_image_upimage_url ? true : false;
            if ($hasOrderBumpImage) {
                $orderBumpImage = File::setUploadedFile($request, 'order_bump_image');
                File::saveUploadedFile($plan, $orderBumpImage, 'order_bump_image_id');
            } else {
                $plan->order_bump_image_id = 0;
                $plan->save();
            }

            $hasUpsellImage = $request->upsell_image_upimage_url ? true : false;
            if ($hasUpsellImage) {
                $upsellImage = File::setUploadedFile($request, 'upsell_image');
                File::saveUploadedFile($plan, $upsellImage, 'upsell_image_id');
            } else {
                $plan->upsell_image_id = 0;
                $plan->save();
            }

            if (!isset($plan->integration->integration_id)) {
                return redirect('/plans');
            }

            $integration = Integration::where('id', $plan->integration->integration_id)->first();

            if (
                verifyIntegration(Constants::CONSTANT_INTEGRATION_GETNET, $plan->platform_id) &&
                $integration->id_webhook === Constants::getKeyIntegration(Constants::CONSTANT_INTEGRATION_GETNET)
            ) {

                if ($updateStatusPlanGetnet === true) {
                    $status = ($plan->status == '0') ? 'inactive' : 'active';
                    $response = $this->getnetPlanService->updateStatusPlan($plan->integration->integration_type_id, $status);

                    if ($response['status'] == 'error') {
                        return redirect('/plans')->withErrors(['message' => $response['data']['response']]);
                    }
                } elseif ($updatePlanGetnet === true) {
                    $response = $this->getnetPlanService->updatePlan($plan->integration->integration_type_id, $request->name, $request->name);

                    if ($response['status'] == 'error') {
                        return redirect('/plans')->withErrors(['message' => $response['data']['response']]);
                    }
                }

                if ($createPlanGetnet === true) {

                    $response = $this->getnetPlanService->updateStatusPlan($plan->integration->integration_type_id, 'inactive');

                    if ($response['status'] == 'error') {
                        return redirect('/plans')->withErrors(['message' => $response['data']['response']]);
                    }

                    $this->storePlanGetnet($request, $plan);
                }
            }

            return redirect('/plans');
        } catch (Exception $e) {
            return redirect()->route('plans.index')->with('error', $e->getMessage());
        }
    }

    public function status($id)
    {
        try {
            $plan = Plan::find($id);
            (new SecurityHelper)->securityUser($plan);
            $plan->status = (int)!$plan->status;
            $plan->save();
            return response()->json(['status' => $plan->status ? 'Ativo' : 'Inativo',]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()], 400);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $subscribers = Subscriber::where('plan_id', $id)->get();
            if ($subscribers->count() > 0) {
                throw new Exception('Este plano possui assinantes cadastrados!');
            }
            $plan = Plan::find($id);
            (new SecurityHelper)->securityUser($plan);
            if ($plan->integration) {
                $integration = Integration::where('id', $plan->integration->integration_id)->first();
            }
            try {
                $plan->delete();
            } catch (Exception $e) {
                throw new Exception('Não foi possível remover esse plano. Existem alunos ou outros planos ligados a este');
            }
            if ($request->ajax()) {
                return response()->json(['status' => 'success', 'message' => 'Plano removido com sucesso']);
            } else {
                return redirect()->route('plans.index');
            }
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['status' => 'error', 'data' => $e, 'message' => $e->getMessage()], 400);
            } else {
                return redirect()->route('plans.index')->with('error', $e->getMessage());
            }
        }
    }

    public function storePlanGetnet($dados, $plan)
    {
        if (verifyIntegration(Constants::CONSTANT_INTEGRATION_GETNET, $plan->platform_id)) {
            $responseApi = $this->getnetPlanService->store($dados, $plan);

            if ($responseApi['status'] !== 'success') {
                return redirect('/plans')->withErrors(['message' => $responseApi['data']->message]);
            }
        }
    }

    public function integrateAll()
    {
        $platform_id = Auth::user()->platform_id;

        if (verifyIntegration('GETNET', $platform_id)) {
            $plans = Plan::where('platform_id', '=', $platform_id)
                ->where('status', '=', 1)
                ->get();

            if ($plans->count() > 0) {

                $cont = $errors = $success = 0;

                foreach ($plans as $plan) {

                    $cont++;

                    $dados = new StdClass;
                    $dados->charge_until = $plan->charge_until;
                    $dados->recurrence = $plan->recurrence;
                    $dados->name = $plan->name;
                    $dados->price = $plan->price;
                    $dados->currency = $plan->currency;

                    $res = $this->getnetPlanService->store($dados, $plan);

                    $response[] = $res;

                    ($res['status'] === 'error') ? $errors++ : $success++;
                }

                $response['total'] = $cont;
                $response['success'] = $success;
                $response['errors'] = $errors;
                dd($response);
            }
            dd('Fim sem registros');
        }
    }

    public function confirmIntegrationGateway($gatewayName, $plan)
    {
        $integration = Integration::where('id_webhook', $gatewayName)
            ->where('platform_id', Auth::user()->platform_id)
            ->first();

        $plan_integration = $plan->integratable->where('integration_id', '=', $integration->id)->first();

        return ($plan_integration !== null && $plan_integration->count() > 0) ? true : false;
    }

    public function emailOn(Request $request)
    {
        if ($request->checked === 'true') {
            $platformEmails = EmailPlatform::where('platform_id', Auth::user()->platform_id)
                ->where('email_id', Email::CONSTANT_EMAIL_NEW_REGISTER)
                ->first();

            if (!$platformEmails) {
                $message = 'Não é possível ativar essa opção enquanto não houver mensagens padrões de e-mail. ';
                $message .= 'Clique <a href="' . config('app.url') . '/emails">aqui</a> para cadastrar.';

                if ($request->plan_id > 0) {
                    $plan = Plan::find($request->plan_id);
                    if ($plan) {
                        $plan->update(['trigger_email' => 0]);
                    }
                }
                return ['status' => 'error', 'message' => $message];
            }
        }
    }

    public function verifyGateway(Request $request)
    {
        if ($request->integration_id) {
            $integration = Integration::where('id', $request->integration_id)->first();
            return ['status' => 'success', 'id_webhook' => $integration->id_webhook];
        }
        return ['status' => 'error',];
    }

    public function storePlanMundipagg($plan)
    {
        if (verifyIntegration(Constants::CONSTANT_INTEGRATION_MUNDIPAGG, $plan->platform_id)) {
            $responseApi = $this->mundipaggPlanService->store($plan);

            if ($responseApi['status'] !== 'success') {
                return redirect('/plans')->withErrors(['message' => $responseApi['data']->message]);
            }
        }
    }

    public function deletePlanMundipagg($plan)
    {
        $response = $this->mundipaggPlanService->deletePlan($plan);

        if ($response['status'] == 'error') {
            return redirect('/plans')->withErrors(['message' => "Erro ao excluir o plano do Mundipagg"]);
        }
    }

    public function replicatePlan(Plan $plan)
    {
        try {
            $copy = $plan->replicate();
            $copy->name = $plan->name . " (cópia)";
            $copy->save();

            $message = 'Produto duplicado com sucesso';
            return response()->json(['status' => 'success', 'data' => $copy, 'message' => $message]);
        } catch (Exception $e) {
            return response()->json(['status' => 'success', 'data' => $e, 'message' => $e->getMessage()], 500);
        }
    }

    public function getPlansByProducts(Request $request, Plan $plans)
    {
        try {
            $plans = $plans->select(
                'plans.id',
                'plans.name'
            )
                ->where('plans.platform_id', Auth::user()->platform_id)
                ->where('plans.status', true)
                ->whereIn('plans.product_id', $request->products)
                ->get();

            return response()->json(['status' => 'success', 'plans' => $plans]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'data' => $e, 'message' => $e->getMessage()], 400);
        }
    }

    public function getPlans(Plan $plans)
    {
        try {
            $plans = $plans->select(
                'plans.id',
                'plans.name'
            )
                ->where('plans.platform_id', Auth::user()->platform_id)
                ->where('plans.status', true)
                ->get();

            return response()->json(['status' => 'success', 'plans' => $plans]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'data' => $e, 'message' => $e->getMessage()], 400);
        }
    }
}
