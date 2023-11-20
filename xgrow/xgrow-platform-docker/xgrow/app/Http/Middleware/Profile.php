<?php

namespace App\Http\Middleware;
use App\Client;

use Closure;
use Illuminate\Support\Facades\Auth;

class Profile
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
		if(Client::isRegistrationComplete()) return redirect('platforms');

        return $next($request);
    }
}
