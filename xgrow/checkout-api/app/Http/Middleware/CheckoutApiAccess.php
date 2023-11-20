<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class CheckoutApiAccess
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
        try {
            $encrypted = Crypt::decrypt($request->header('Content-Xtk'));
            if( ( time() - $encrypted['xtk']) <= 60  ) {
                return $next($request);
            }
        }
        catch (\Exception $e) {
            return $this->forbidden($request);
        }
        return $this->forbidden($request);
    }

    private function forbidden($request)
    {
        Log::info('[Checkout Api Access] Exception XTK', ['request' => $request->all()]);
        return response()->json(['status' => 'success', 'token' => Hash::make('success')], 402);
    }
}
