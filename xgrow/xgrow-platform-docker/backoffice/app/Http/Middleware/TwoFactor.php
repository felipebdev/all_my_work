<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Auth;

class TwoFactor
{

    public function handle($request, Closure $next)
    {
        /** @var \App\User $user */
        $user = Auth::user();

        //dd($user);
        if (Auth::check() && $user->two_factor_enabled && $user->two_factor_code) {
            if ($user->isTwoFactorCodeExpired()) {
                $user->resetTwoFactorCode();
                Auth::logout();

                return redirect()->route('login')
                    ->withMessage('Código de verificação expirado. Por favor realize login novamente.');
            }

            if (!$request->is('verify*')) {
                return redirect()->guest(route('verify.index'));
            }
        }

        return $next($request);
    }
}
