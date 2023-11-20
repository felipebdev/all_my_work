<?php

namespace App\Http\Controllers;

use App\AccessLog;
use App\Notifications\ConfirmationResetPasswordLANotification;
use App\Notifications\SubscriberResetPasswordLANotification;
use App\Platform;
use App\Safe;
use App\Services\LA\CacheClearService;
use App\Subscriber;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class AuthController extends Controller
{
    private $subscriber;
    private $safe;
    private $platform;
    private CacheClearService $cacheClearService;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(Subscriber $subscriber, Safe $safe, Platform $platform, CacheClearService $cacheClearService)
    {
        $this->middleware('auth:api', [
            'except' => [
                'login', 'checksIfExists', 'checksIfEmailExists', 'sendResetLinkEmail', 'resetLAPassword'
            ]
        ]);
        $this->subscriber = $subscriber;
        $this->safe = $safe;
        $this->platform = $platform;
        $this->cacheClearService = $cacheClearService;
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password', 'platform_id']);
        $credentials['status'] = ['active', 'trial'];

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => true, 'message' => 'Dados inválidos']);
        }


        $subscriber = auth('api')->user();

        if ($subscriber->status !== Subscriber::STATUS_TRIAL && $subscriber->status !== Subscriber::STATUS_ACTIVE) {
            return response()->json(['error' => true, 'message' => 'Cadastro desativado, entre em contato com o dono da plataforma!' . $subscriber->status]);
        }

        //$this->checkLastLogout($subscriber);

        AccessLog::create([
            'user_id' => $subscriber->id,
            'user_type' => $subscriber->getTable(),
            'type' => 'LOGIN',
            'description' => 'Usuário ' . $subscriber->email . ' efetuou login no site [via api]',
            'platform_id' => $subscriber->platform_id,
            'ip' => $_SERVER["REMOTE_ADDR"],
            'browser_type' => AccessLog::searchBrowser('API'),
            'device_type' => AccessLog::searchDevice('API')
        ]);
        date_default_timezone_set('America/Sao_Paulo');
        $dataHora = date('Y-m-d H:i:s');
        $subscriber->update(['login' => $dataHora, 'last_acess' => $dataHora]);

        return response()->json([
            'token' => $token,
            'subscriber' => $subscriber,
            'ttl' => auth('api')->factory()->getTTL()
        ], 200);
    }


    public function checksIfExists(Request $request)
    {

        $type = $request->input('check_type');
        $type_to_string = (string)$type;
        $valor = $request->input($type_to_string);

        try {

            $subscriber = $this->subscriber->where($type, $valor)->first();

            if ($subscriber)
                return response()->json(['status' => 'success']);

            return response()->json(['status' => 'error']);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkToken()
    {
        return response()->json(auth()->check());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        try {

            $subscriber = $this->subscriber->where('id', auth('api')->user()->id)->first();

            AccessLog::create([
                'user_id' => $subscriber->id,
                'user_type' => $subscriber->getTable(),
                'type' => 'LOGOUT',
                'description' => 'Usuário ' . $subscriber->email . ' saiu do site [via api]',
                'platform_id' => $subscriber->platform_id,
                'ip' => $_SERVER["REMOTE_ADDR"],
                'browser_type' => AccessLog::searchBrowser('API'),
                'device_type' => AccessLog::searchDevice('API')
            ]);

            auth()->logout(true);

            return response()->json([
                'status' => 'success',
                'response' => "success logout",
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);
        }
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL()
        ], 200, [
            'Authorization' => $token
        ]);
    }

    private function checkLastLogout($subscriber)
    {
        $logout = AccessLog::select('type')
            ->where('user_id', $subscriber->id)
            ->where('user_type', 'subscribers')
            ->orderBy('created_at', 'DESC')
            ->first();

        if ($logout !== null) {

            if ($logout->type === 'LOGIN') {

                $query = 'SELECT MAX(created_at) as last_iteration
                  FROM content_logs a
                  WHERE user_type = "subscribers"
                  AND user_id = ' . $subscriber->id;

                $result = DB::select($query);

                if (count($result) > 0) {
                    $accessLog = AccessLog::create([
                        'user_id' => $subscriber->id,
                        'user_type' => $subscriber->getTable(),
                        'type' => 'LOGOUT',
                        'description' => 'Usuário ' . $subscriber->email . ' saiu do site [via api]',
                        'platform_id' => $subscriber->platform_id,
                        'ip' => $_SERVER["REMOTE_ADDR"],
                        'browser_type' => AccessLog::searchBrowser('API'),
                        'device_type' => AccessLog::searchDevice('API')
                    ]);
                    $accessLog->created_at = $result[0]->last_iteration;
                    $accessLog->save();
                }
            }
        }
    }

    /* This Function sent the Token for the subscriber */
    public function resetPassword(Request $request)
    {
        try {
            $email = $request->input('email');
            $platform_id = $request->input('platform_id');

            $platform = Platform::find($platform_id);

            $subscriber = $this->subscriber
                ->where('email', $email)
                ->where('platform_id', $platform_id)
                ->first();
            if ($subscriber) {
                //$subscriber->reset_password = Carbon::now();
                //$subscriber->save();
                $token = Hash::make($subscriber->reset_password . $subscriber->email);
                $token = md5(date('Y-d-m H:i:s')) . md5($subscriber->name);
                $site = $request->input('url_site');

                if (isset($request->url_type)) {
                    $url = $site . '?token=' . $token . '&email=' . $email;
                } else {
                    $url = $site . '/index.html?token=' . $token . '&email=' . $email;
                }

                Mail::send('emails.password-recovery', [
                    'platform' => $platform,
                    'subscriber' => $subscriber,
                    'url' => $url
                ], function ($message) use ($subscriber) {
                    $message->subject('Recuperação de senha');
                    $message->from(env('MAIL_FROM_ADDRESS', 'no-reply@xgrow.com.br'), env('MAIL_FROM_NAME', 'XGrow'));
                    $message->to($subscriber->email);
                });

                return response()->json(['status' => 'success', 'message' => 'Link de redefinição de senha enviado com sucesso!']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'E-mail não existe']);
            }
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);
        }
    }

    /* This Function reset the password for the subscriber */
    public function newSubscriberPassword(Request $request)
    {
        try {
            $email = $request->input('email');
            $platform_id = $request->input('platform_id');
            //            $cpf = $request->input('cpf');
            $password = $request->input('password');

            $subscriber = $this->subscriber
                ->where('email', $email)
                ->where('platform_id', $platform_id)
                //                 ->where('cpf', $cpf)
                ->first();

            if ($subscriber) {
                //Validate Hash and time
                $reset_password = new Carbon($subscriber->reset_password);
                //if( !Hash::check($subscriber->reset_password.$subscriber->email, $request->token) || $reset_password->diffInMinutes(Carbon::now()) > 60 ) {
                //    return response()->json(['error' => true, 'status' => 'error', 'message' => 'Tempo expirado, gere um novo link de redefinição de senha']);
                //}
                $subscriber->raw_password = $password;
                //$subscriber->reset_password = null;
                $subscriber->save();
                $url = $request->input('url_site');
                Mail::send('emails.password-changed', ['subscriber' => $subscriber, 'url' => $url], function ($message) use ($subscriber) {
                    $message->subject('Alteração de senha');
                    $message->from(env('MAIL_FROM_ADDRESS', 'no-reply@xgrow.com.br'), env('MAIL_FROM_NAME', 'Fandone'));
                    $message->to($subscriber->email);
                });

                return response()->json(['status' => 'success', 'message' => 'Sua senha foi redefinida com sucesso!']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'E-mail não existe']);
            }
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);
        }
    }

    /* This is a verification if the provider is spam */
    public function checksIfEmailExists()
    {
        $email = request(['email']);

        try {
            $subscriber = $this->subscriber->where('email', $email)->first();

            if ($subscriber)
                return response()->json(['status' => 'success']);

            return response()->json(['status' => 'error', 'message' => 'E-mail não existe']);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);
        }
    }

    // Password Recovery OK
    public function sendResetLinkEmail(Request $request)
    {
        try {
            $email = $request->input('email');
            $platform_id = $request->input('platform_id');
            $isLANew = $request->input('new');
            $site = $request->input('url_site');

            if (!$email || !$platform_id || !$site) {
                return response()->json(['error' => true, 'response' => 'Dados incompletos. Verifique os dados enviados.'], 400);
            }

            $platform = Platform::find($platform_id);

            if (!$platform) {
                return response()->json(['error' => true, 'response' => 'Não encontramos nenhum dado referente a esta Área de Aprendizado.'], 400);
            }

            $user = DB::table('subscribers')
                ->where(['platform_id' => $platform_id, 'email' => $email])
                ->first();

            if (!$user) {
                return response()->json(['error' => true, 'response' => 'Não encontramos nenhuma informação para os dados informados.'], 400);
            }

            DB::table('password_resets')->where(['email' => $email])->delete();

            $token = md5(date('Y-d-m H:i:s') . generateRandomString(32));

            DB::table('password_resets')->insert([
                'email' => $email,
                'token' => $token,
                'created_at' => Carbon::now()
            ]);

            if ($isLANew) {
                $url = $site . '?token=' . $token . '&email=' . $email;
            } else {
                $url = $site . '/index.html?token=' . $token . '&email=' . $email;
            }

            $data = [
                'token' => $token,
                'name' => $user->name,
                'url' => $url,
                'email' => $email,
                'platformName' => $platform->name ?? ''
            ];
            Notification::route('mail', $email)->notify(new SubscriberResetPasswordLANotification($data));
            $this->cacheClearService->clearSubscriberCache($platform_id, $user->email, $user->id);
            return response()->json(['status' => 'success', 'message' => 'Link de redefinição de senha enviado com sucesso!']);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);
        }
    }

    public function resetLAPassword(Request $request)
    {
        try {
            $email = $request->input('email');
            $platform_id = $request->input('platform_id');
            $token = $request->input('token');
            $password = $request->input('password');
            $repassword = $request->input('repassword');
            $url = $request->input('url_site');

            if (!$email || !$platform_id || !$token || !$password || !$repassword || !$url) {
                return response()->json(['error' => true, 'response' => 'Dados incompletos. Verifique os dados enviados.'], 400);
            }

            if ($password != $repassword) {
                return response()->json(['error' => true, 'response' => 'Senhas não coincidem.'], 400);
            }

            $recovery = DB::table('password_resets')
                ->where(['token' => $token, 'email' => $email])
                ->first();

            if (!$recovery) {
                return response()->json(['error' => true, 'response' => 'Token ou email inválidos.'], 400);
            }

            $now = Carbon::now();
            $recoveryDateTime = Carbon::make($recovery->created_at);
            $expired = $now->diffInMinutes($recoveryDateTime);

            if ($expired > 30) {
                return response()->json(['error' => true, 'response' => 'Token expirado.'], 400);
            }

            DB::table('subscribers')
                ->where(['platform_id' => $platform_id, 'email' => $email])
                ->update(['password' => Hash::make($password)]);

            $platform = DB::table('platforms')->where(['id' => $platform_id])->first();

            if (!$platform) {
                return response()->json(['error' => true, 'response' => 'Área de Aprendizado não encontrada.'], 400);
            }

            $user = DB::table('subscribers')->where(['platform_id' => $platform_id, 'email' => $email])->first();

            if (!$user) {
                return response()->json(['error' => true, 'response' => 'Usuário não encontrado.'], 400);
            }

            $data = [
                'name' => $user->name,
                'platformName' => $platform->name,
                'url_site' => $url,
            ];

            Notification::route('mail', $email)->notify(new ConfirmationResetPasswordLANotification($data));

            return response()->json(['status' => 'success', 'message' => 'Sua senha foi redefinida com sucesso!']);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);
        }
    }
}
