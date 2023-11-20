<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Notifications\TwoFactorCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class TwoFactorController extends Controller
{

    protected $errorMessage = 'Código de verificação inválido';

    public function index()
    {
        $email = Auth::user()->email;

        if (!App::environment('production')) {
            // "inject" code into request on non-production environments
            request()->request->add(['code' => Auth::user()->two_factor_code]);
        }

        return view('auth.two-factor', ['email' => $this->maskEmail($email)]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'two_factor_code' => 'integer|required',
        ], [
            'two_factor_code.*' => $this->errorMessage,
        ]);

        $user = Auth::user();
        if (
            $request->input('two_factor_code') == $user->two_factor_code
            && !$user->isTwoFactorCodeExpired()
        ) {
            $user->resetTwoFactorCode();

            return redirect()->route('choose.platform');
        }

        return redirect()->back()->withErrors([
            'two_factor_code' => $this->errorMessage,
        ]);
    }

    public function resend()
    {
        $user = Auth::user();
        $user->generateTwoFactorCode();
        $user->notify(new TwoFactorCode());

        return redirect()->back()->withMessage('Código de verificação reenviado');
    }


    /**
     * Mask email username
     *
     * Eg:
     * "a@example.com" -> "*@example.com"
     * "ab@example.com" -> "**@example.com"
     * "abc@example.com" -> "***@example.com"
     * "abcd@example.com" -> "a**d@example.com"
     * "abcde@example.com" -> "a***e@example.com"
     *
     * @param  string  $email
     * @return string
     */
    private function maskEmail(string $email): string
    {
        list($name, $domain) = explode('@', $email);

        $len = strlen($name);
        if ($len <= 3) {
            $maskedName = str_repeat('*', $len);
        } else {
            $mask = str_repeat('*', $len - 2);
            $maskedName = substr_replace($name, $mask, 1, strlen($mask));
        }

        return "{$maskedName}@{$domain}";
    }
}
