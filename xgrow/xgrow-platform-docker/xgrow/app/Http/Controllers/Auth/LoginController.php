<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Notifications\TwoFactorCode;
use App\Rules\ReCAPTCHAv3;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/platforms';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }


   /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        if(env('APP_ENV') === 'local') return;

        $request->validate([
            $this->username() => Rule::exists('platforms_users')->where(function ($query) {$query->where('active', 1);}),
            'password' => 'required|string',
            'grecaptcha' => ['required', new ReCAPTCHAv3],
        ], ['Usuário não localizado ou inativo.']);
    }

    protected function authenticated(Request $request, $user)
    {
        if ($user->two_factor_enabled) {
            $user->generateTwoFactorCode();
            $user->notify(new TwoFactorCode());
        }
    }

}
