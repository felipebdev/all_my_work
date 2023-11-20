<?php

namespace App\Http\Controllers;

use App\Client;
use App\File;
use App\Helpers\BigBoostHelper;
use App\Services\Checkout\CheckoutBaseService;
use GuzzleHttp\Exception\ClientException;
use App\Http\Requests\FirstAccessRequest;
use App\Http\Requests\ValidateDocumentsRequest;
use App\Http\Traits\CustomResponseTrait;
use App\Mail\SendEmailUserRegistering;
use App\Mail\SendMailSupport;
use App\Platform;
use App\PlatformUser;
use App\Rules\ReCAPTCHAv3;
use App\Services\LAService;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Ramsey\Uuid\Uuid;

/**
 *
 */
class PlatformUserController extends Controller
{

    use CustomResponseTrait;

    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        $user = PlatformUser::select([
            'name', 'surname', 'email',
            'display_name', 'whatsapp',
            'instagram', 'linkedin',
            'facebook', 'thumb_id'
        ])->find(Auth::user()->id);
        return view('platforms-users.index', compact('user'));
    }

    /**
     * @param Request $request
     * @return Application|RedirectResponse|Redirector
     */
    public function store(Request $request)
    {
        $thumb = File::setUploadedFile($request, 'thumb');
        $user = PlatformUser::find(Auth::user()->id);
        File::saveUploadedFile($user, $thumb, 'thumb_id');
        return redirect('/home');
    }

    /**
     * @return Application|Factory|View
     */
    public function create()
    {
        $data = [];

        $platforms = Platform::all();

        $data["platforms"] = $platforms;
        $data["type"] = "create";

        return view('platforms-users.create', $data);
    }


    /**
     * @param $id
     * @return Application|Factory|View
     */
    public function edit($id)
    {
        $data = [];

        $platforms = Platform::all();

        $user = PlatformUser::find($id);

        $data["platforms"] = $platforms;
        $data["user"] = $user;
        $data["type"] = "edit";

        return view('platforms-users.edit', $data);
    }

    /**
     * @param Request $request
     * @return Application|RedirectResponse|Redirector
     * @throws Exception
     */
    public function update(Request $request)
    {
        if (!$request->input('confirm_data')) return redirect('/platforms')->with('error', 'Para salvar as alterações, digite sua senha no campo de confirmação');

        $userPass = PlatformUser::select(['password', 'email'])->find(Auth::user()->id);
        $confirm = Hash::check($request->input('confirm_data'), $userPass->password);
        if (!$confirm) return redirect('/platforms')->with('error', 'A senha informada não confere com o usuário.');

        $user = PlatformUser::find(Auth::user()->id);
        $user->email = $request->input('user_email');
        if ($userPass->email !== $user->email) return redirect('/platforms')->with('error', 'Os dados informados não conferem.');

        $user->name = $request->input('user_name');
        $user->surname = $request->input('user_surname');
        $user->display_name = $request->input('user_displayname');
        $user->whatsapp = $request->input('whatsapp');
        $user->instagram = $request->input('instagram');
        $user->linkedin = $request->input('linkedin');
        $user->facebook = $request->input('facebook');

        if (!empty($request->input('user_password'))) {
            passwordStrength($request->input('user_password') ?? 0);
            $user->password = Hash::make($request->input('user_password'));
        };

        $thumb = File::setUploadedFile($request, 'thumb');
        File::saveUploadedFile($user, $thumb, 'thumb_id');

        $user->save();

        return redirect('/user');
    }

    /**
     * @param $id
     * @return Application|RedirectResponse|Redirector
     */
    public function destroy($id)
    {
        $platform = PlatformUser::find($id);

        $platform->delete();

        return redirect('/platforms/users');
    }

    public function emailSupport(Request $request): RedirectResponse
    {
        $reasons = config('constants.emailSupport.reasonContact');

        $platformUser = PlatformUser::find(Auth::user()->id);

        $emailData = [
            'reason' => $reasons[$request->reason],
            'subject' => $request->subject,
            'message' => $request->message
        ];

        $usersTo = ['suporte@xgrow.com'];

        Mail::to($usersTo)->send(new SendMailSupport($platformUser, $emailData));

        return back()->with(['message' => 'E-mail enviado com sucesso!']);
    }

    /**
     * @return Application|Factory|View
     */
    public function support()
    {
        $user = PlatformUser::find(Auth::user()->id);
        return view('platforms-users.support.index', compact('user'));
    }

    /**
     * @param $email
     * @return JsonResponse
     */
    public function checkEmailBeforeRegistering($email): JsonResponse
    {
        if (PlatformUser::where('email', $email)->first()) {
            return $this->customJsonResponse('Falha ao realizar ação', 422, ['errors' => "O e-mail $email já esta em uso"]);
        }

        return $this->customJsonResponse('O E-mail digitado não consta em nossos registros!');
    }

    /** Post register flow
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        try {
            $validation = Validator::make($request->all(), [
                'name' => 'required|min:3',
                'email' => 'required|email|unique:platforms_users',
                'phone' => 'required',
                'accepted_terms' => 'accepted',
                'grecaptcha' => ['required', new ReCAPTCHAv3],
            ]);

            if ($validation->fails()) {
                throw new Exception($validation->errors()->toJson(), 422);
            }

            $password = substr(str_shuffle('abcdefghijklmnopqrstuvxywzABCDEFGHIJKLMNOPQRSTUVXYWZ0123456789!@#$%&*()-.+'), 18, 8);

            $platformUser = new PlatformUser();
            $platformUser->name = $request->input('name');
            $platformUser->email = $request->input('email');
            $platformUser->whatsapp = $request->input('phone');
            $platformUser->password = Hash::make($password);
            $platformUser->accepted_terms = $request->input('accepted_terms');
            $platformUser->save();

            $accessData = [
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => $password
            ];

            Mail::to($request->input('email'))->send(new SendEmailUserRegistering($accessData));

            return $this->customJsonResponse('Cadastrado realizado com sucesso!', 201);
        } catch (Exception $e) {
            return $this->customJsonResponse('Falha ao realizar ação', 400, ['errors' => json_decode($e->getMessage())]);
        }
    }

    public function registerNewClient(Request $request)
    {
        if (Auth::user()) return redirect('/home');
        return view('platforms.register');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function firstAccess(FirstAccessRequest $request): JsonResponse
    {
        try {
            $client = $this->createClient([
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'email' => Auth::user()->email,
                'password' => Auth::user()->password,
                'phone_number' => Auth::user()->whatsapp,
                'document' => $request->input('document')
            ]);

            $platformId = $this->createPlatform(
                [
                    'name' => Auth::user()->name,
                    'name_slug' => \Str::slug(Auth::user()->name, '-'),
                    'customer_id' => $client->id
                ]
            );

            DB::table('platform_user')->insert([
                'platform_id' => $platformId,
                'platforms_users_id' => Auth::user()->id,
                'type_access' => 'full'
            ]);

            try {
                $this->createDefaultTheme($platformId);
            } catch (Exception $e) {
                throw new Exception("Falha ao criar o design da plataforma: $platformId", 400);
            }

            return $this->customJsonResponse('Cadastrado realizado com sucesso!', 201);
        } catch (Exception $e) {
            return $this->customJsonResponse('Falha ao realizar ação', 400, ['errors' => json_decode($e->getMessage())]);
        }
    }


    /**
     * @param array $data
     * @return mixed
     * @throws Exception
     */
    public function createPlatform(array $data)
    {
        $uuid = Uuid::uuid4();

        Platform::insert([
            'id' => $uuid,
            'name' => $data['name'],
            'url' => env('APP_URL_LEARNING_AREA', 'https://la.xgrow.com') . '/' . $uuid,
            'name_slug' => $data['name_slug'],
            'customer_id' => $data['customer_id'],
            'created_at' => Carbon::now()
        ]);

        return $uuid;
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function createClient(array $data)
    {
        $client = new Client();
        $client->percent_split = Client::PERCENT_SPLIT;
        $client->tax_transaction = Client::TAX_TRANSACTION;
        $client->first_name = $data['first_name'];
        $client->last_name = $data['last_name'];
        $client->email = $data['email'];
        $client->password = $data['password'];
        $client->phone_number = $data['phone_number'];
        $client->type_person = strlen($data['document']) === 11 ? 'F' : 'J';
        $client->cpf = strlen($data['document']) === 11 ? $data['document'] : null;
        $client->cnpj = strlen($data['document']) === 14 ? $data['document'] : null;
        $client->verified = 0;
        $client->save();

        return $client;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updateAddress(Request $request): JsonResponse
    {
        $request->validate([
            'zipcode' => 'required',
            'address' => 'required',
            'district' => 'required',
            'city' => 'required',
            'state' => 'required'
        ]);

        $userEmail = Auth::user()->email;

        Client::where('email', $userEmail)->update([
            'zipcode' => $request->input('zipcode'),
            'address' => $request->input('address'),
            'number' => $request->input('number'),
            'district' => $request->input('district'),
            'city' => $request->input('city'),
            'state' => $request->input('state'),
            'complement' => $request->input('complement'),
        ]);

        return $this->customJsonResponse('Endereço atualizado com sucesso!', 201);
    }

    /**
     * @param ValidateDocumentsRequest $request
     * @param string $documentFace
     * @return JsonResponse
     * @throws \JsonException
     */
    public function validateDocuments(ValidateDocumentsRequest $request, string $documentFace): JsonResponse
    {
        try {

            $image = $_FILES['file'];

            $extension = pathinfo($image['name'], PATHINFO_EXTENSION);

            $image['name'] = Uuid::uuid4() . '.' . $extension;

            $client = Client::where('email', Auth::user()->email)->first();

            if (!$client) {

                return $this->customJsonResponse('Usuário possui cadastro incompleto!', 422, ['errors' => 'Usuário possui cadastro incompleto!']);
            }

            $bigBoostHelper = new BigBoostHelper;

            $s3 = Storage::disk('documents');

            $document = $client->cpf ?? $client->cnpj;

            $bigIDResult = $bigBoostHelper->ocrDocument($image, $document);

            if (array_key_exists('validate_error', $bigIDResult) && $bigIDResult['validate_error'] == true) {
                return $this->customJsonResponse($bigIDResult['message'], $bigIDResult['code'], ['errors' => $bigIDResult['message']]);
            }

            $uploadDirectory = $client->upload_directory != null
                ? $client->upload_directory
                : strtolower($client->first_name . '-' . $client->last_name);

            $client->check_document_number = array_key_exists('IDENTIFICATIONNUMBER', $bigIDResult['DocInfo'])
                ? $bigIDResult['DocInfo']['IDENTIFICATIONNUMBER']
                : $client->check_document_number;

            $client->verified = 1;

            $client->check_document_type = 1;

            $imageContent = file_get_contents($image['tmp_name']);

            $s3->put($image['name'], $imageContent, $uploadDirectory);

            return $this->updateClientInformation($s3, $uploadDirectory, $image['name'], $documentFace, $client);
        } catch (Exception $e) {

            $message = $e ?? 'Erro desconhecido';

            Log::error('Erro na aplicação ' . $message);

            return $this->customJsonResponse('Falha ao realizar ação', 400, ['errors' => json_decode($message)]);
        }
    }

    /**
     * @param Filesystem $s3
     * @param string $uploadDirectory
     * @param $name
     * @param string $documentFace
     * @param $client
     * @return JsonResponse
     */
    public function updateClientInformation(Filesystem $s3, string $uploadDirectory, $name, string $documentFace, $client): JsonResponse
    {
        try {

            $s3ImageURL = $s3->url($uploadDirectory . '/' . $name);

            $client->document_front_image_url = $s3ImageURL;

            $createRecipient = $this->createRecipient();

            if (!$createRecipient) {
                return $this->customJsonResponse('Não foi possivel criar o recebedor.', 422, ['errors' => 'Não foi possivel criar o recebedor.']);
            }

            $client->save();

            return $this->customJsonResponse('Documento validado com sucesso!', 201);
        } catch (Exception $e) {

            return $this->customJsonResponse('Falha ao realizar ação', 400, ['errors' => $e->getMessage()]);
        }
    }

    /**
     * @return \GuzzleHttp\Client
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function checkoutBaseService()
    {
        $checkoutBaseService = new CheckoutBaseService;

        return $checkoutBaseService->connectionConfig(
            Auth::user()->platform_id,
            Auth::user()->id
        );
    }

    /**
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function createRecipient()
    {
        try {

            $res = $this->checkoutBaseService()->post('recipients');

            Log::info('Recebedor criado com sucesso', [$res->getBody()]);

            return true;
        } catch (ClientException $e) {

            Log::error('Não foi possível criar recebedor', [$e]);

            return false;
        } catch (ConnectException $e) {

            Log::error('Não foi possível conectar ao servidor de destino', [$e]);

            return false;
        }
    }

    /** Get client short data
     * @return JsonResponse
     */
    public function getClientData(): JsonResponse
    {
        try {
            $client = Client::where('email', Auth::user()->email)->first();

            if ($client) {

                return $this->customJsonResponse('Dados carregados com sucesso!', 200, [
                    'clientIdentity' => ($client->type_person === "F") ? $client->cpf : $client->cnpj,
                    'name' => $client->first_name . ' ' . $client->last_name,
                    'type' => $client->type_person
                ]);
            } else {

                return $this->customJsonResponse('Cliente não encontrado para este usuário', 200, ['errors' => json_decode('Cliente não encontrado para este usuário')]);
            }
        } catch (Exception $e) {

            return $this->customJsonResponse('Falha ao realizar ação', 400, ['errors' => json_decode($e->getMessage())]);
        }
    }

    /**
     * @param $platformId
     */
    public function createDefaultTheme($platformId)
    {
        $defaultTheme = [
            "backgroundColor" => "#191414",
            "backgroundImageUrl" => "https://d1rbsosh8yoado.cloudfront.net/PLATFORM_UPLOADS/$platformId/0416ecdd-ed0a-4854-a16b-2dd3d7890e84-banner_xgrow.webp",
            "logoUrl" => "https://la-xgrow.sfo3.digitaloceanspaces.com/180d5f0237148d777de10569e24bfae6-grayLogo.svg",
            "faviconUrl" => "https://la-xgrow.sfo3.digitaloceanspaces.com/5d537df9692dba4e6d13769f3fb6da5d-favicon.ico",
            "bannerUrl" => "https://xgrow-dev.us-east-1.linodeobjects.com/54138015-2c29-4e80-b949-291ce481ddf7.jpg",
            "description" => "Meta descricao da plataforma",
            "keywords" => "xgrow",
            "platformId" => $platformId,
            "platformName" => "Nome da Plataforma",
            "primaryColor" => "#91bc1e",
            "secondaryColor" => "#c3ce01",
            "tertiaryColor" => "#282a2b",
            "textColor" => "#ffffff",
            "inputColor" => "#292929",
            "backgroundType" => "gradient",
            "backgroundGradientFirst" => "#0f1314",
            "backgroundGradientSecond" => "#262829",
            "backgroundGradientDegree" => 86,
            "borderRadius" => 50,
            "title" => "Titulo da Plataforma",
            "footer" => "Agradecimentos plataforma Xgrow. (Rodapé)",
            "supportNumber" => "",
            "supportEmail" => Auth::user()->email ?? 'suporte@email.com',
            "supportLink" => "",
            "supportType" => "email",
        ];

        try {

            $laService = new LAService($platformId, Auth::user()->id);

            return $laService->postStartLA($defaultTheme);
        } catch (Exception $e) {

            Log::error("Não foi possível cadastrar o tema default para a plataforma $platformId " . json_encode($e));
        }
    }
}
