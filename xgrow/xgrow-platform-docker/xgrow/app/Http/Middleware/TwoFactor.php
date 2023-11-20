<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Auth;

class TwoFactor
{

    public function handle($request, Closure $next)
    {
        /** @var \App\PlatformUser $user */
        $user = Auth::user();

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
