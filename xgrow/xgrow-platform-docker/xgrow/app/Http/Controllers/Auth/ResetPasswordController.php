<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Exception;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use App\Subscriber;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    private $subscriber;

    public function __construct(Subscriber $subscriber)
    {
        $this->subscriber = $subscriber;

    }

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/home';


    protected function broker()
    {
        return Password::broker(request()->get('user_type'));
    }

    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email, 'user_type' => $request->user_type]
        );
    }


    public function repasswd(Request $request)
    {
        try {
            $user = $this->subscriber->where('email', $request->email)->where($request->check_type, $request->type_value)->first();
            $user->raw_password = $request->password;
            $user->save();
            return response()->json(['status' => 'success', 'message' => 'senha redefinida com sucesso!']);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);
        }

    }

    public function resetForApi(Request $request)
    {
        try {
            $user = $this->subscriber->where('email', $request->email)->where($request->check_type, $request->type_value)->first();
            $user->raw_password = $request->password;
            $user->save();
            return response()->json(['status' => 'success', 'message' => 'senha redefinida com sucesso!']);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);
        }
    }

    /**
     * Reset the given user's password. REESCREVER
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function reset(Request $request)
    {
        try {
            $data = $request->validate($this->rules(), $this->validationErrorMessages());

            $recovery = DB::table('password_resets')
                ->where('email', $data['email'])
                ->first();

            if (!($recovery and \Hash::check($data['token'], $recovery->token))) {
                throw new Exception('Token ou email inválidos.');
            }

            $now = Carbon::now();
            $recoveryDateTime = Carbon::make($recovery->created_at);
            $expired = $now->diffInMinutes($recoveryDateTime);

            if ($expired > 30) {
                throw new Exception('Token expirado.');
            }

            $response = $this->broker()->reset(
                $this->credentials($request), function ($user, $password) {
                $this->resetPassword($user, $password);
            });

            return $response == Password::PASSWORD_RESET
                ? $this->sendResetResponse($request, $response)
                : $this->sendResetFailedResponse($request, $response);
        } catch (Exception $e) {
            return redirect()
                ->route('password.reset')
                ->withErrors(['email' => $e->getMessage()
                ]);
        }
    }
}
