<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class VerifyIpMiddleware
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
        if (Auth::user()->active == 0 OR $this->checkIfIpIsBlocked()) {

            auth()->logout();

            return redirect()->route('login')->withErrors(['ip' => ['Acesso não permitido.']]);
        } 

        return $next($request);
        
    }

    private function checkIfIpIsBlocked(){
        $restrictIps = Auth::user()->platforms->pluck('restrict_ips')->toArray();
        $ipsAvailable = Auth::user()->platforms->pluck('ips_available')->toArray();
        return in_array(1, $restrictIps) and !in_array($_SERVER["REMOTE_ADDR"], $ipsAvailable);
    }

}
