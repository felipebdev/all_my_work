<?php

namespace App\Http\Middleware;

use App\CheckoutAttempt;
use Closure;
use Illuminate\Support\Facades\Crypt;

class CheckoutApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (CheckoutAttempt::check($request->header('token'))) {
            $request->merge(Crypt::decrypt($request->header('token')));
            return $next($request);
        }
        return $this->forbidden();
    }

    private function forbidden()
    {
        return response()->json(['message' => 'Limite de tentativas excedido, atualize a página e tente novamente'], 403);
    }
}
