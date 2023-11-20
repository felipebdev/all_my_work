<?php

namespace App\Http\Controllers;

use App\Constants;
use App\Course;
use App\CourseProduct;
use App\File;
use App\Helpers\CollectionHelper;
use App\Helpers\SecurityHelper;
use App\Http\Controllers\Api\LearningAreaController;
use App\Http\Traits\CustomResponseTrait;
use App\Integration;
use App\Plan;
use App\PlanCategory;
use App\PlanResources;
use App\Product;
use App\Section;
use App\SectionProduct;
use App\Services\Auth\ClientStatus;
use App\Services\Checkout\RecipientsStatusService;
use App\Services\LA\CacheClearService;
use App\Services\Producer\ProducerService;
use App\Subscriber;
use App\Subscription;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    use CustomResponseTrait;

    private ProducerService $producerService;
    private CacheClearService $cacheClearService;
    private RecipientsStatusService $recipientsStatusService;

    public function __construct(
        ProducerService $producerService,
        CacheClearService $cacheClearService,
        RecipientsStatusService $recipientsStatusService
    ) {
        $this->producerService = $producerService;
        $this->cacheClearService = $cacheClearService;
        $this->recipientsStatusService = $recipientsStatusService;
    }

    public function index(Request $request)
    {
        $query = Plan::with([
            'integratable.integration' => function ($query) {
                $query->select('id', 'id_webhook', 'id_integration');
            }
        ])
            ->with('product')
            ->where('platform_id', Auth::user()->platform_id);

        if ($request->ajax()) {
            return datatables()->eloquent($query)->make();
        }

        $status = ClientStatus::withPlatform(Auth::user()->platform_id, Auth::user()->email);

        return view('products.index', [
            'totalLabel' => getTotalLabel($query, 'produto'),
            'clientApproved' => $status->clientApproved,
            'recipientStatusMessage' => $status->recipientStatusMessage,
            'verifyDocument' => $status->mustVerify,
        ]);
    }

    public function indexNext(Request $request)
    {
        $status = ClientStatus::withPlatform(Auth::user()->platform_id, Auth::user()->email);

        return view('products.index-next', [
            'verifyDocument' => $status->mustVerify,
            'recipientStatusMessage' => $status->recipientStatusMessage,
        ]);
    }

    public function getAllProducts(Request $request)
    {
        $offset = $request->input('offset') ?? 25;
        $search = $request->input('search') ?? '';

        try {
            $products = Product::query()
                ->select([
                    'products.id',
                    'products.name',
                    'products.type',
                    'products.only_sell',
                    'products.external_learning_area',
                    'products.internal_learning_area',
                    'products.created_at',
                    'plans.price',
                    'products.status',
                ])
                ->where('products.platform_id', Auth::user()->platform_id)
                ->where('products.name', 'like', "%$search%")
                ->when($request->productTypes, function ($query, $types) {
                    $query->whereIn('products.type', $types);
                })
                ->when($request->deliveryTypes, function ($query) use ($request) {
                    $query->where(function ($query) use ($request) {
                        if (in_array('onlySell', $request->deliveryTypes)) {
                            $query->orWhere('products.only_sell', 1);
                        }
                        if (in_array('external', $request->deliveryTypes)) {
                            $query->orWhere('products.external_learning_area', 1);
                        }
                        if (in_array('internal', $request->deliveryTypes)) {
                            $query->orWhere('products.internal_learning_area', 1);
                        }
                    });
                })
                ->when($request->status, function ($query, $status) {
                    $query->whereIn('products.status', $status);
                })
                ->withCount('subscribers')
                ->join('plans', 'products.id', '=', 'plans.product_id')
                ->groupBy('id')->get();

            $collection = CollectionHelper::paginate($products, $offset);
            return $this->customJsonResponse('Dados carregados com sucesso.', Response::HTTP_OK, ['products' => $collection]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage() . ' ' . $e->getLine(), 400, []);
        }
    }

    public function edit(Request $request, $id)
    {
        try {
            $product = new Product();
            $product->name = $request->name;
            $product->description = $request->description;
            $product->type = $request->type;
            $product->platform_id = Auth::user()->platform_id;
            $product->category_id = $request->category_id;
            $product->support_email = $request->support_email;
            $product->keywords = $request->keywords;
            $image = File::setUploadedFile($request, 'image');
            $product->save();

            File::saveUploadedFile($product, $image, 'image_id');

            return redirect()->route('products.plan', [$product->id]);
        } catch (\Exception $e) {
            return back()->with('error', 'Erro: ' . $e->getMessage());
        }
    }

    public function listPlans($id)
    {
        $query = Plan::select('id', 'name', 'price', 'status', 'type_plan', 'platform_id as platform', 'allow_change')
            ->where('product_id', $id)
            ->orderBy('created_at', 'DESC');

        return datatables()->eloquent($query)->make();
    }

    public function affiliateLink($id)
    {
        $payload = [
            'platformId' => Auth::user()->platform_id,
            'producerId' => Auth::user()->id,
            'platformName' => Auth::user()->platform->name,
            'producerName' => Auth::user()->name . ' ' . Auth::user()->surname,
            'planId' => $id
        ];
        $secret = config('jwtplatform.jwt_clean_cache_la') ?? 'secret';
        $jwt = \Firebase\JWT\JWT::encode($payload, $secret, 'HS256');
        return "https://afiliados.xgrow.com/producer/{$id}?auth=$jwt";
    }

    public function editPlan($id)
    {
        try {
            $learningArea = new LearningAreaController();
            $learningArea->producerAccess();

            $product = Product::find($id);
            if (!$product) throw new Exception('Plano ou produto não encontrado');
            $plan = Plan::where('product_id', $id)->first();
            if (!$plan) throw new Exception('Plano ou produto não encontrado');

            (new SecurityHelper)->securityUser($product);

            $plans = Plan::where('product_id', $id)->get();

            $categories = PlanCategory::orderBy('name')->pluck('name', 'id')->all();
            $layout_checkout = Plan::LAYOUT_CHECKOUT;
            $platforms_support = Plan::PLATFORMS_SUPPORT;

            // Hashtag Generator
            $keywords = [];
            if ($product->keywords) {
                $keyword = explode(';', $product->keywords);
                foreach ($keyword as $kw) {
                    if ($kw != '') array_push($keywords, '#' . $kw);
                }
                $keywords = implode(' ', $keywords);
            }
            $keywords = is_array($keywords) ? '' : $keywords;

            // Keywords to select2
            $keywordToSelect = $product->keywords ? explode(";", $product->keywords) : '';

            $hasSuppport = (int)!is_null($product->checkout_address);
            $hasCheckout = (int)(!is_null($product->checkout_support) || !is_null($product->checkout_email));

            $link  = array();
            foreach ($plans as $c => $p) {
                $link[$p->id] = $this->affiliateLink($p->id);
            }

            foreach ($plans as $plan) {
                $recipientErrors = [];
                try {
                    $recipientErrors = $this->recipientsStatusService->getRecipientsPlanErrors($plan->id);
                } catch (GuzzleException $e) {
                    report($e); // only report, ignore communication errors
                }

                if ($recipientErrors) {
                    $plan->checkout_url = 'Recebedor(es) com pendência: '
                        . implode('; ', $recipientErrors)
                        . '. Favor contatar o suporte';
                } else {
                    $plan->checkout_url = config('app.url_checkout') . '/' . $plan->platform_id . '/' . base64_encode($plan->id);
                }
            }

            return view('products.edit.index', compact(
                'product',
                'plan',
                'keywords',
                'keywordToSelect',
                'plans',
                'categories',
                'layout_checkout',
                'platforms_support',
                'hasSuppport',
                'hasCheckout',
                'link'
            ));
        } catch (\Exception $e) {
            return redirect()
                ->route('products.index')
                ->with('error', 'Erro: ' . $e->getMessage());
        }
    }

    public function replicateProduct($id)
    {
        try {
            $product = Product::findOrFail($id);
            $plans = Plan::where(['product_id' => $id])->get();

            $productCopy = $product->replicate();
            $productCopy->name = $product->name . " (cópia)";
            $productCopy->save();

            $favoritePlan = 0;
            foreach ($plans as $plan) {
                $copy = $plan->replicate();
                $copy->name = $plan->name . " (cópia)";
                $copy->product_id = $productCopy->id;
                $copy->save();
                $favoritePlan = $copy->id;
            }

            $productCopy->favorite_plan = $favoritePlan;
            $productCopy->save();

            $message = 'Produto duplicado com sucesso';
            return response()->json(['status' => 'success', 'data' => $copy, 'message' => $message]);
        } catch (Exception $e) {
            return response()->json(['status' => 'success', 'data' => $e, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
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
                if (
                    verifyIntegration(Constants::CONSTANT_INTEGRATION_MUNDIPAGG, $plan->platform_id) &&
                    $integration->id_webhook === Constants::getKeyIntegration(Constants::CONSTANT_INTEGRATION_MUNDIPAGG)
                ) {
                    //$this->deletePlanMundipagg($plan);
                }
            }
            try {
                $plan->delete();
            } catch (Exception $e) {
                throw new Exception('Não foi possível remover esse plano.');
            }
            return response()->json(['status' => 'success', 'message' => 'Recurso removido com sucesso']);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function list(Request $request)
    {
        try {
            if (Auth::isProducer()) {
                $products = $this->producerService->listProductsFromProducer(Auth::user()->id, false);
            } else {
                $products = Product::select('id', 'name')->where('platform_id', Auth::user()->platform_id)->get();
            }
            return response()->json(['status' => 'success', 'products' => $products]);
        } catch (Exception $e) {
            return response()->json(['status' => 'success', 'data' => $e, 'message' => $e->getMessage()], 500);
        }
    }

    public function statusProduct($id)
    {
        try {
            $product = Product::find($id);
            (new SecurityHelper)->securityUser($product);
            $product->status = !$product->status;
            $product->save();
            return response()->json(['status' => $product->status ? 'Ativo' : 'Inativo',]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()], 400);
        }
    }

    public function statusPlan($id)
    {
        try {
            $plan = Plan::findOrFail($id);
            (new SecurityHelper)->securityUser($plan);
            $plan->status = (bool)!$plan->status;
            $plan->save();
            return response()->json(['status' => $plan->status ? 'Ativo' : 'Inativo',]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()], 400);
        }
    }

    public function create(Request $request)
    {
        if ($request->has('type')) {
            if (!in_array($request->input('type'), ['R', 'P'])) {
                return redirect()->route('products.create');
            }
        }
        $product = new Product;
        $categories = PlanCategory::orderBy('name')->pluck('name', 'id')->all();
        return view('products.create', compact('product', 'categories'));
    }

    public function store(Request $request)
    {
        try {
            $platformId = Auth::user()->platform_id;

            if (!empty($request->keywords)) {
                $keyword_dump = '';
                foreach ($request->keywords as $keyword) {
                    $keyword_dump .= $keyword . ';';
                }
                $request->request->add(['keywords' => $keyword_dump]);
            }
            $product = new Product();
            $product->name = $request->name;
            $product->description = $request->description;
            $product->type = $request->type;
            $product->platform_id = $platformId;
            $product->category_id = $request->category_id;
            $product->support_email = $request->support_email;
            $product->keywords = $request->keywords;
            $product->status = 1;
            $image = File::setUploadedFile($request, 'image');
            $product->save();

            $plan = new Plan();
            $plan->name = $product->name;
            $plan->currency = 'BRL';
            $plan->recurrence = 1;
            $plan->status = 1;
            $plan->platform_id = $platformId;
            $plan->type_plan = $product->type;
            $plan->trigger_email = true;
            $plan->product_id = $product->id;
            $plan->category_id = $product->category_id;
            $plan->charge_until = 0;
            $plan->save();

            $product->favorite_plan = $plan->id;
            $product->save();

            File::saveUploadedFile($product, $image, 'image_id');

            return redirect()->route('products.plan', $plan->id);
        } catch (\Exception $e) {
            return back()->with('error', 'Erro: ' . $e->getMessage());
        }
    }

    public function productPlan(Request $request, $id)
    {
        try {
            $plan = Plan::findOrFail($id);
            $product = Product::findOrFail($plan->product_id);
            SecurityHelper::securityMultipleUser($product);
            $currency = Product::CURRENCIES;
            $upsellOptions = Product::UPSELL_OPTIONS;
            $installments = Product::INSTALLMENTS;
            $orderBumps = Product::where([
                'platform_id' => Auth::user()->platform_id
            ])
                ->where('id', '<>', $product->id)
                ->pluck('name', 'id')->all();

            $upSells = Product::where([
                'platform_id' => Auth::user()->platform_id
            ])
                ->where('id', '<>', $product->id)
                ->pluck('name', 'id')->all();

            return view('products.create', compact('currency', 'orderBumps', 'installments', 'product', 'upsellOptions', 'plan', 'upSells'));
        } catch (\Exception $e) {
            return redirect()
                ->route('products.index')
                ->with('error', 'Erro: ' . $e->getMessage());
        }
    }

    public function storePlan(Request $request, $id)
    {

        try {
            $plan = Plan::findOrFail($id);

            $rules = [
                "currency" => "required",
                "price" => "required",
                "installment" => [
                    "required",
                    function ($attribute, $value, $fail) use ($request, $plan) {
                        $price = str_replace(',', '.', str_replace('.', '', $request->price));
                        $maxInstallment = intval($price / 4);
                        if ($maxInstallment < ($value ?? 12)) {
                            $fail('O número máximo de parcelas é inválido. O valor mínimo da parcela deve ser de R$4,00.');
                        }
                    }
                ]
            ];

            $request->validate($rules);

            $plan->recurrence = $request->input('recurrence') ?? 1;
            $plan->currency = $request->input('currency') ?? 'BRL';
            $plan->price = str_replace(',', '.', str_replace('.', '', $request->input('price')));
            $plan->installment = (int)$request->input('installment');
            $plan->payment_method_credit_card = $request->has('payment_method_credit_card');
            $plan->payment_method_boleto = $request->has('payment_method_boleto');
            $plan->payment_method_pix = $request->has('payment_method_pix');
            $plan->payment_method_multiple_cards = $request->has('payment_method_multiple_cards');
            $plan->payment_method_multiple_means = $request->has('payment_method_multiple_means');
            $plan->unlimited_sale = $request->has('unlimited_sale');
            $plan->charge_until = $request->input('charge_until') ?? 0;
            $plan->checkout_payout_limit = $request->input('checkout_payout_limit') ?? 2;

            if ($request->has('use_promotional_price')) {
                $plan->use_promotional_price = $request->has('use_promotional_price');
                $plan->promotional_price = str_replace(',', '.', str_replace('.', '', $request->input('promotional_price')));
                $plan->promotional_periods = $request->input('promotional_periods') ?? null;
            }

            $plan->url_checkout_confirm = $request->input('url_checkout_confirm');

            $plan->save();

            return redirect()->route('products.delivery', $plan->id);
        } catch (\Exception $e) {

            return back()->with('error', 'Erro: ' . $e->getMessage() . ' ' . $e->getLine());
        }
    }

    public function productDelivery(Request $request, $id)
    {
        try {
            $learningArea = new LearningAreaController();
            $learningArea->producerAccess();

            $plan = Plan::findOrFail($id);
            $product = Product::findOrFail($plan->product_id);

            SecurityHelper::securityMultipleUser($product);

            $planDelivery = Product::DELIVERY_PLANS;
            $platformId = Auth::user()->platform_id;
            $courses = Course::where('platform_id', $platformId)->get();
            $sections = Section::where('platform_id', $platformId)->get();

            $coursePlan = CourseProduct::where('product_id', $product->id)->get();
            $sectionPlan = SectionProduct::where('product_id', $product->id)->get();
            $coursePlanPluck = CourseProduct::where('product_id', $product->id)->pluck('course_id')->toArray();
            $sectionPlanPluck = SectionProduct::where('product_id', $product->id)->pluck('section_id')->toArray();
            $hasDelivery = ($coursePlan->count() > 0 || $sectionPlan->count() > 0);

            $subjectEmail = "Bem-vindo";
            $messageEmail = "Olá ##NOME_ASSINANTE##,
Seus dados de acesso são os abaixo:
Login: ##EMAIL_ASSINANTE##
Senha: ##AUTO##
Link de acesso: " . env("APP_URL_LEARNING_AREA", "https://learningarea.xgrow.com") . "/" . $platformId;

            return view('products.create', compact(
                'product',
                'plan',
                'planDelivery',
                'courses',
                'sections',
                'coursePlan',
                'sectionPlan',
                'coursePlanPluck',
                'sectionPlanPluck',
                'hasDelivery',
                'subjectEmail',
                'messageEmail'
            ));
        } catch (\Exception $e) {
            return back()->with('error', 'Erro: ' . $e->getMessage());
        }
    }

    public function attachCourseOrSectionToProduct(Request $request)
    {
        try {
            $product = Product::findOrFail($request->idProduct);
            if ($request->typeContent === 'c') {
                $courseProduct = CourseProduct::where(['product_id' => $product->id, 'course_id' => $request->idContent])->first();
                if (!$courseProduct) {
                    CourseProduct::create(['product_id' => $product->id, 'course_id' => $request->idContent]);
                }
            } elseif ($request->typeContent === 's') {
                $sectionProduct = SectionProduct::where(['product_id' => $product->id, 'section_id' => $request->idContent])->first();
                if (!$sectionProduct) {
                    SectionProduct::create(['product_id' => $product->id, 'section_id' => $request->idContent]);
                }
            } else {
                throw new Exception('Conteúdo inválido.');
            }
            $this->clearDeliveryCache($request);

            return $this->customJsonResponse('Curso/Seção adicionado com sucesso.', 200);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    public function totalSubscribersByProduct(Request $request)
    {
        try {
            $product = Product::findOrFail($request->input('idProduct'));
            $plan = Plan::where('product_id', $product->id)->first();
            $total = Subscription::select(['subscriber_id'])->where('plan_id', $plan->id)->count();
            return $this->customJsonResponse('Conteúdo removido com sucesso.', 200, ['total' => $total ?? 0]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    public function detachCourseOrSectionToProduct(Request $request)
    {
        try {
            $product = Product::findOrFail($request->idProduct);
            if ($request->typeContent === 'c') {
                $courseProduct = CourseProduct::where(['product_id' => $product->id, 'course_id' => $request->idContent])->first();
                if ($courseProduct) $courseProduct->delete();
            } elseif ($request->typeContent === 's') {
                $sectionProduct = SectionProduct::where(['product_id' => $product->id, 'section_id' => $request->idContent])->first();
                if ($sectionProduct) $sectionProduct->delete();
            } else {
                throw new Exception('Conteúdo inválido.');
            }

            return $this->customJsonResponse('Curso/Seção removido com sucesso.', 200);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    public function setDelivery(Request $request)
    {
        try {
            $product = Product::findOrFail($request->input('idProduct'));
            $typeDelivery = $request->input('type_delivery');

            if ($typeDelivery === 'onlySell') {
                $product->only_sell = true;
                $product->external_learning_area = false;
                $product->internal_learning_area = false;
            }

            if ($typeDelivery === 'external') {
                $product->external_learning_area = true;
                $product->internal_learning_area = false;
                $product->only_sell = false;
            }

            if ($typeDelivery === 'internal') {
                $product->subject_email = $request->input('subject_email') ?? $product->subject_email;
                $product->message_email = $request->input('message_email') ?? $product->message_email;
                $product->internal_learning_area = true;
                $product->external_learning_area = false;
                $product->only_sell = false;
            }

            $product->save();
            return response()->json(['status' => 'success', 'message' => '']);
        } catch (Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Ocorreu ao alterar dados. Motivo: ' . $e->getMessage()
            ], 400);
        }
    }

    public function unlimitedDelivery(Request $request)
    {
        try {
            $product = Product::findOrFail($request->input('idProduct'));
            $product->unlimited_delivery = !$product->unlimited_delivery;
            $product->save();
            return response()->json(['status' => 'success', 'message' => '']);
        } catch (Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Ocorreu um erro ao remover dados. Motivo: ' . $e->getMessage()
            ], 400);
        }
    }

    public function productInfo($id)
    {
        try {
            $product = Product::find($id);
            if (!$product) throw new Exception('Plano ou produto não encontrado');
            $plan = Plan::where('product_id', $id)->first();
            if (!$plan) throw new Exception('Plano ou produto não encontrado');

            // Hashtag Generator
            $keywords = [];
            if ($product->keywords) {
                $keyword = explode(';', $product->keywords);
                foreach ($keyword as $kw) {
                    if ($kw != '') array_push($keywords, '#' . $kw);
                }
                $keywords = implode(' ', $keywords);
            }
            $keywords = is_array($keywords) ? '' : $keywords;
            SecurityHelper::securityMultipleUser($product);
            return view('products.create', compact('product', 'plan', 'keywords'));
        } catch (\Exception $e) {
            return redirect()->route('products.index')->with('error', 'Erro: ' . $e->getMessage());
        }
    }

    public function listCheckoutLinks($id)
    {
        try {
            $product = Product::findOrFail($id);
            $plan = Plan::select(['id', 'url_checkout_confirm', 'price', 'name'])->where('product_id', $product->id)->get();
            return response()->json(['status' => 'success', 'data' => $plan]);
        } catch (Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Ocorreu um erro ao remover dados. Motivo: ' . $e->getMessage()
            ], 400);
        }
    }

    public function config(Request $request, int $id)
    {
        try {
            $product = Product::find($id);
            if (!$product) throw new Exception('Plano ou produto não encontrado');
            SecurityHelper::securityMultipleUser($product);

            if (!empty($request->keywords)) {
                $keyword_dump = '';
                foreach ($request->keywords as $keyword) {
                    $keyword_dump .= $keyword . ';';
                }
                $request->request->add(['keywords' => $keyword_dump]);
            }

            $product->name = $request->name;
            $product->description = $request->description;
            $product->category_id = $request->category_id;
            $product->support_email = $request->support_email;
            $product->keywords = $request->keywords;
            $product->checkout_support_platform = $request->checkout_support_platform;
            $product->checkout_support = $request->checkout_support;
            $product->checkout_email = $request->checkout_email;
            $product->checkout_layout = $request->checkout_layout;
            $product->checkout_address = $request->has('checkout_address');
            $product->double_email = $request->has('double_email');
            $image = File::setUploadedFile($request, 'image');
            $product->save();
            File::saveUploadedFile($product, $image, 'image_id');

            return redirect()->route('products.edit-plan', $product->id)->with('success', 'Produto atualizado com sucesso.');
        } catch (\Exception $e) {
            return redirect()->route('products.index')->with('error', 'Erro: ' . $e->getMessage());
        }
    }

    public function getOrderBumpsAndUpSell($productPlanId, $type)
    {
        try {
            $orderBumps = PlanResources::select('id', 'product_plan_id', 'plan_id', 'type', 'image_id')
                ->where('product_plan_id', $productPlanId)
                ->where('type', $type)
                ->where('platform_id', Auth::user()->platform_id)
                ->with('plans:id,name,price')
                ->with('image:id,filename')
                ->get()
                ->toArray();

            return response()->json(['status' => 'success', 'data' => $orderBumps]);
        } catch (Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Ocorreu um erro ao buscar dados. Motivo: ' . $e->getMessage()
            ], 400);
        }
    }

    public function saveResource(Request $request)
    {
        try {
            $plan = Plan::find($request->input('plan'));
            $product = Plan::where('product_id', (int)$request->input('product'))
                ->where('status', 1)
                ->first();

            $planResources = new PlanResources();
            $planResources->product_id = $plan->product_id;
            $planResources->product_plan_id = $plan->id;
            $planResources->plan_id = $product->id;
            $planResources->platform_id = Auth::user()->platform_id;
            $planResources->type = $request->input('type');
            $planResources->discount = $request->input('discount');
            $planResources->message = $request->input('message');
            if ($request->file('image')) {
                $image = File::setUploadedSingleFile($request->file('image'));
                $planResources->image_id = $image->id;
            }
            if ($request->input('type') === 'U') {
                $planResources->video_url = $request->input('video_url');
                $planResources->accept_event = $request->input('accept_event');
                $planResources->decline_event = $request->input('decline_event');
                $planResources->accept_url = $request->input('accept_url');
                $planResources->decline_url = $request->input('decline_url');
            }
            $planResources->save();

            $resource = $request->input('type') === 'O' ? 'Order Bump' : 'Upsell';
            return response()->json(['status' => 'success', 'message' => "$resource salvo com sucesso."]);
        } catch (Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Ocorreu um erro ao salvar dados. Motivo: ' . $e->getMessage() . ' ' . $e->getLine()
            ], 400);
        }
    }

    public function updateResource(Request $request)
    {
        try {
            $product = Plan::where('product_id', (int)$request->input('product'))
                ->where('status', 1)
                ->first();

            SecurityHelper::securityMultipleUser($product);
            $resource = PlanResources::find((int)$request->input('resource'));
            SecurityHelper::securityMultipleUser($resource);
            $resource->discount = $request->input('discount');
            $resource->message = $request->input('message');
            $resource->discount = $request->input('discount');
            $resource->plan_id = $product->id;
            if ($request->file('image')) {
                $image = File::setUploadedSingleFile($request->file('image'));
                $resource->image_id = $image->id;
            }
            if ($resource->type === 'U') {
                $resource->video_url = $request->input('video_url');
                $resource->accept_event = $request->input('accept_event');
                $resource->decline_event = $request->input('decline_event');
                $resource->accept_url = $request->input('accept_url');
                $resource->decline_url = $request->input('decline_url');
            }
            $resource->save();
            $resource = $resource->type === 'O' ? 'Order Bump' : 'Upsell';
            return response()->json(['status' => 'success', 'message' => "$resource atualizado com sucesso."]);
        } catch (Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Ocorreu um erro ao salvar dados. Motivo: ' . $e->getMessage()
            ], 400);
        }
    }

    public function getResource($id)
    {
        try {
            $resource = PlanResources::select(
                'id',
                'message',
                'plan_id',
                'discount',
                'image_id',
                'video_url',
                'accept_event',
                'decline_event',
                'accept_url',
                'decline_url'
            )
                ->where('id', $id)
                ->with('image:id,filename,original_name')
                ->first()
                ->toArray();
            $product = Plan::find($resource['plan_id']);
            $resource['plan_id'] = $product->product_id;
            return response()->json(['status' => 'success', 'data' => $resource]);
        } catch (Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Ocorreu um erro ao buscar dados. Motivo: ' . $e->getMessage()
            ], 400);
        }
    }

    public function deleteResource(Request $request)
    {
        try {
            $resource = PlanResources::find($request->input('resource'));
            $resource->delete();
            $resource = $resource->type === 'O' ? 'Order Bump' : 'Upsell';
            return response()->json(['status' => 'success', 'message' => "$resource removido com sucesso."]);
        } catch (Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Ocorreu um erro ao buscar dados. Motivo: ' . $e->getMessage()
            ], 400);
        }
    }

    public function getDelivery(Request $request)
    {
        try {
            $productId = $request->input('product');
            $platformId = Auth::user()->platform_id;

            $courseProduct = CourseProduct::select(['course_id as course'])
                ->where('product_id', $productId)
                ->get();

            $sectionProduct = SectionProduct::select(['section_id as section'])
                ->where('product_id', $productId)
                ->get();

            $courses = Course::select(['id', 'name'])
                ->where([
                    'platform_id' => $platformId,
                    'active' => true
                ])->get();
            $sections = Section::select(['id', 'name'])
                ->where([
                    'platform_id' => $platformId,
                    'active' => true
                ])->get();

            $product = Product::select([
                'internal_learning_area as internal_area',
                'external_learning_area as external_area',
                'only_sell as only_sell',
                'subject_email as email',
                'message_email as message',
            ])->findOrFail($productId);

            $hasCourse = $courseProduct->count() > 0;
            $hasSection = $sectionProduct->count() > 0;

            return response()->json([
                'status' => 'success',
                'px_courses' => $courseProduct->toArray(),
                'px_sections' => $sectionProduct->toArray(),
                'has' => ['course' => $hasCourse, 'section' => $hasSection],
                'product' => $product,
                'courses' => $courses->toArray(),
                'sections' => $sections->toArray(),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Ocorreu um erro ao buscar dados. Motivo: ' . $e->getMessage() . ' L' . $e->getLine()
            ], 400);
        }
    }

    public function favoritePlan(Request $request)
    {
        try {
            $productId = $request->input('product');
            $plan = $request->input('plan');
            $product = Product::findOrFail($productId);
            $product->favorite_plan = $plan;
            $product->save();
            $product->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Plano atualizado com sucesso.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Ocorreu um erro ao alterar dados. Motivo: ' . $e->getMessage() . ' L' . $e->getLine()
            ], 400);
        }
    }

    // Routes for more than one plan
    public function createPlanByProduct($id)
    {
        try {
            $product = Product::findOrFail($id);
            $plan = new Plan();
            $plan->name = '';
            $plan->currency = 'BRL';
            $plan->recurrence = 1;
            $plan->status = 1;
            $plan->platform_id = $product->platform_id;
            $plan->type_plan = $product->type;
            $plan->trigger_email = true;
            $plan->product_id = $product->id;
            $plan->category_id = $product->category_id;
            $plan->charge_until = 0;
            $plan->save();

            // Save favorite plan as last
            $product->favorite_plan = $plan->id;
            $product->save();

            return redirect()->route('products.new.plan.product', $plan->id);
        } catch (\Exception $e) {
            return back()->with('error', 'Erro: ' . $e->getMessage());
        }
    }

    public function isolatedProductPlan($id) //EDIT
    {
        try {
            $plan = Plan::findOrFail($id);
            $product = Product::findOrFail($plan->product_id);
            SecurityHelper::securityMultipleUser($product);
            $currency = Product::CURRENCIES;
            $upsellOptions = Product::UPSELL_OPTIONS;
            $installments = Product::INSTALLMENTS;
            $orderBumps = Product::where([
                'platform_id' => Auth::user()->platform_id
            ])
                ->where('id', '<>', $product->id)
                ->pluck('name', 'id')->all();

            $upSells = Product::where([
                'platform_id' => Auth::user()->platform_id
            ])
                ->where('id', '<>', $product->id)
                ->pluck('name', 'id')->all();

            return view('products.isolated.form', compact(
                'currency',
                'orderBumps',
                'upSells',
                'installments',
                'product',
                'upsellOptions',
                'plan'
            ));
        } catch (\Exception $e) {
            return redirect()
                ->route('products.index')
                ->with('error', 'Erro: ' . $e->getMessage());
        }
    }

    public function isolatedStorePlan(Request $request, $id)
    {
        try {
            $hasPaymentMethodFree = $request->has('payment_method_free');
            $plan = Plan::findOrFail($id);

            $price = 0;
            $promotional_price = 0;

            if(!$hasPaymentMethodFree){
                $rules = [
                    "currency" => "required",
                    "price" => "required",
                    "installment" => [
                        "required",
                        function ($attribute, $value, $fail) use ($request, $plan) {
                            $price = str_replace(',', '.', str_replace('.', '', $request->price));
                            $maxInstallment = intval($price / 4);
                            if ($maxInstallment < ($value ?? 12)) {
                                $fail('O número máximo de parcelas é inválido. O valor mínimo da parcela deve ser de R$4,00.');
                            }
                        }
                    ]
                ];

                $request->validate($rules);

                $price = str_replace(',', '.', str_replace('.', '', $request->price));
                $promotional_price = str_replace(',', '.', str_replace('.', '', $request->input('promotional_price')));
            }
            else{
                //Delete all order bumps in payment method free update
                PlanResources::whereProductPlanId($id)->whereType('O')->delete();
            }

            $sales = null;
            if ($plan->price <> $price || $plan->use_promotional_price <> $request->has('use_promotional_price') || $plan->promotional_price <> $promotional_price) {
                $sales = DB::table('payment_plan')->where('plan_id', $plan->id)->first();
            }

            if ($plan->price <> $price) {
                if (!is_null($sales))
                    return redirect()
                        ->route('products.edit.plan.product', $plan->id)
                        ->withErrors('O Valor do plano não pode ser alterado. O plano possui vendas cadastradas.');
            }

            if ($plan->promotional_price <> $promotional_price) {
                if (!is_null($sales))
                    return redirect()
                        ->route('products.edit.plan.product', $plan->id)
                        ->withErrors('O Valor Diferenciado do plano não pode ser alterado. O plano possui vendas cadastradas.');
            }

            if ($plan->use_promotional_price <> $request->has('use_promotional_price')) {
                if (!is_null($sales))
                    return redirect()
                        ->route('products.edit.plan.product', $plan->id)
                        ->withErrors('O Valor Diferenciado do plano não pode ser alterado. O plano possui vendas cadastradas.');
            }

            if ($request->has('chk-greeting-exists')) {
                if (!filter_var($request->input('url_checkout_confirm'), FILTER_VALIDATE_URL)) {
                    return redirect()
                        ->route('products.edit.plan.product', $plan->id)
                        ->withErrors('A URL de confirmação digitada, não é uma URL válida.');
                }
            }

            $plan->name = $request->input('name');
            $plan->recurrence = $request->input('recurrence') ?? 1;
            $plan->currency = $request->input('currency') ?? 'BRL';
            $plan->price = str_replace(',', '.', str_replace('.', '', $request->input('price')));
            $plan->installment = (int)$request->input('installment');
            $plan->payment_method_free = $hasPaymentMethodFree;
            $plan->payment_method_credit_card = $request->has('payment_method_credit_card');
            $plan->payment_method_boleto = $request->has('payment_method_boleto');
            $plan->payment_method_pix = $request->has('payment_method_pix');
            $plan->payment_method_multiple_cards = $request->has('payment_method_multiple_cards');
            $plan->unlimited_sale = $request->has('unlimited_sale');
            $plan->charge_until = $request->input('charge_until') ?? 0;
            $plan->checkout_payout_limit = $request->input('checkout_payout_limit') ?? 2;

            $plan->use_promotional_price = $request->input('use_promotional_price');

            if ($request->input('use_promotional_price')) {
                $plan->use_promotional_price = !is_null($request->input('use_promotional_price'));
                $plan->promotional_price = str_replace(',', '.', str_replace('.', '', $request->input('promotional_price')));
                $plan->promotional_periods = $request->input('promotional_periods') ?? null;
            }

            $plan->url_checkout_confirm = $request->input('url_checkout_confirm');

            $plan->save();

            return redirect()->route('products.edit-plan', $plan->product_id);
        } catch (\Exception $e) {
            return back()->with('error', 'Erro: ' . $e->getMessage() . ' ' . $e->getLine());
        }
    }

    public function getListProducts()
    {
        try {
            $products = Product::query()
                ->select([
                    'products.id',
                    'products.name',
                    'products.type',
                    'products.created_at',
                    'products.status',
                ])
                ->where('products.platform_id', Auth::user()->platform_id)
                ->when(Auth::isProducer(), function ($q) {
                    $allowedProducts = $this->producerService->listProductsFromProducer(Auth::user()->id);
                    return $q->whereIn('products.id', $allowedProducts);
                })
                ->join('plans', 'products.id', '=', 'plans.product_id')
                ->groupBy('id');
            return DataTables::eloquent($products)->escapeColumns([])->toJson();
        } catch (Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Ocorreu um erro ao alterar dados. Motivo: ' . $e->getMessage() . ' L' . $e->getLine()
            ], 400);
        }
    }

    public function getProducts(Request $request)
    {
        try {
            $data = $request->only(['columns']);

            $cvalue = $data['columns'][3]['search']['value'];
            $data['columns'][3]['search']['value'] = 1;

            if (strpos($cvalue, "internal") !== false) {
                $data['columns'][3]['name'] = "products.internal_learning_area";
            } else if (strpos($cvalue, "external") !== false) {
                $data['columns'][3]['name'] = "products.external_learning_area";
            } else if (strpos($cvalue, "onlySell") !== false) {
                $data['columns'][3]['name'] = "products.only_sell";
            } else {
                //exclui colunna delivery  e reordena array para evitar erro
                $data['columns'][3] = $data['columns'][4];
                $data['columns'][4] = $data['columns'][5];
                $data['columns'][5] = $data['columns'][6];
            }

            $request->request->add(['columns' => $data['columns']]);

            $products = Product::query()
                ->select([
                    'products.id',
                    'products.name',
                    'products.type',
                    'products.only_sell',
                    'products.external_learning_area',
                    'products.internal_learning_area',
                    'products.created_at',
                    'plans.price',
                    'products.status',
                ])
                ->where('products.platform_id', Auth::user()->platform_id)
                ->withCount('subscribers')
                ->join('plans', 'products.id', '=', 'plans.product_id')
                ->groupBy('id');
            return DataTables::eloquent($products)->escapeColumns([])->toJson();
        } catch (Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Ocorreu um erro ao alterar dados. Motivo: ' . $e->getMessage() . ' L' . $e->getLine()
            ], 400);
        }
    }

    public function updateImageResource(Request $request)
    {
        try {
            $resource = PlanResources::find((int)$request->input('resource'));
            SecurityHelper::securityMultipleUser($resource);
            $resource->image_id = 0;
            $resource->save();

            $resource = $resource->type === 'O' ? 'Order Bump' : 'Upsell';
            return response()->json(['status' => 'success', 'message' => "$resource atualizado com sucesso."]);
        } catch (Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Ocorreu um erro ao salvar dados. Motivo: ' . $e->getMessage()
            ], 400);
        }
    }

    public function deleteProduct($id)
    {
        try {
            $product = Product::findOrFail($id);
            $plans = Plan::where('product_id', $product->id)->get();
            if ($plans->count() > 1) {
                foreach ($plans as $plan) {
                    $resources = PlanResources::where('plan_id', $plan->id)->get();
                    if (count($resources) == 0) {
                        $plan->delete();
                    }
                }
            }
            if ($plans->count() === 1) {
                $plans = Plan::where('product_id', $product->id)->first();
                $plans->delete();
            }
            $product->delete();
            return response()->json(['status' => 'success', 'message' => "Produto removido com sucesso."]);
        } catch (Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Motivo: ' . $e->getMessage()
            ], 400);
        }
    }

    public function destroyPlan(Request $request, $id)
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
                // For clear garbage plan
                if ($plan->name === "" && (int)$plan->price === 0) {
                    $plan->forceDelete();
                } else {
                    $plan->delete();
                }
            } catch (Exception $e) {
                throw new Exception('Não foi possível remover esse plano. Existem alunos ou outros planos ligados a este');
            }
            return response()->json(['status' => 'success', 'message' => 'Plano removido com sucesso']);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'data' => $e, 'message' => $e->getMessage()], 400);
        }
    }

    public function updateAffiliationEnabled(Request $request, $productId)
    {
        $product = Product::with('affiliationSettings')->findOrFail($productId);
        $product->affiliation_enabled = $request->input('affiliation_enabled') ?? false;
        $product->save();

        return response()->json([
            'error' => false,
            'message' => 'Atualizado com sucesso!',
            'data' => $product
        ]);
    }

    public function planAllowChange($planId)
    {
        try {
            $platform_id = Auth::user()->platform_id;
            $plan = Plan::find($planId);

            if (is_null($plan))
                return $this->customJsonResponse('Plano não encontrado.', 400);

            if ($plan->platform_id <> $platform_id)
                return $this->customJsonResponse('Plano não pertence a plataforma.', 400);

            $plan->allow_change = !$plan->allow_change;

            $plan->save();

            return $this->customJsonResponse('Plano atualizado com sucesso.', 200);
        } catch (Exception $e) {

            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }


    /**
     * Function responsible for sync GraphQL course and Section
     * with Product delivery
     *
     * @return JsonResponse|void  */
    public function attachContentOnProduct(Request $request)
    {
        try {
            $product = Product::findOrFail($request->idProduct);
            if ($request->typeContent === 'c') {
                $courseProduct = CourseProduct::where(['product_id' => $product->id, 'content_course_id' => $request->idContent])->first();
                if (!$courseProduct) {
                    CourseProduct::create(['product_id' => $product->id, 'content_course_id' => $request->idContent]);
                }
            } elseif ($request->typeContent === 's') {
                $sectionProduct = SectionProduct::where(['product_id' => $product->id, 'content_section_id' => $request->idContent])->first();
                if (!$sectionProduct) {
                    SectionProduct::create(['product_id' => $product->id, 'content_section_id' => $request->idContent]);
                }
            } else {
                throw new Exception('Conteúdo inválido.');
            }

            return $this->customJsonResponse('Curso/Seção adicionado com sucesso.', 200);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    /**
     * Function responsible for sync GraphQL course and Section
     * with Product delivery
     *
     * @return JsonResponse|void  */
    public function detachContentOnProduct(Request $request)
    {
        try {
            $product = Product::findOrFail($request->idProduct);
            if ($request->typeContent === 'c') {
                $courseProduct = CourseProduct::where(['product_id' => $product->id, 'content_course_id' => $request->idContent])->first();
                if ($courseProduct) $courseProduct->delete();
            } elseif ($request->typeContent === 's') {
                $sectionProduct = SectionProduct::where(['product_id' => $product->id, 'content_section_id' => $request->idContent])->first();
                if ($sectionProduct) $sectionProduct->delete();
            } else {
                throw new Exception('Conteúdo inválido.');
            }

            return $this->customJsonResponse('Curso/Seção removido com sucesso.', 200);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    /**
     * List all contents attached on this product
     *
     * @return JsonResponse|void  */
    public function contentAttachedOnProduct(Request $request)
    {
        try {
            $courses = CourseProduct::where('product_id', $request->idProduct)->get()->pluck('content_course_id');
            $sections = SectionProduct::where('product_id', $request->idProduct)->get()->pluck('content_section_id');

            return $this->customJsonResponse('Curso/Seção removido com sucesso.', 200, ['courses' => $courses, 'sections' => $sections]);
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    /**
     * List all contents attached on this product
     *
     * @return JsonResponse|void  */
    public function clearDeliveryCache(Request $request)
    {
        try {
            $this->cacheClearService->clearAllCachesFromSubscribers(Auth::user()->platform_id);

            return $this->customJsonResponse('Delivery Salvo com sucesso. Essa atualização pode levar até 5 minutos');
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }
}
