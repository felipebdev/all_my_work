<?php

namespace App\Http\Middleware\mobile;

use Closure;
use Illuminate\Support\Facades\Auth;

/**
 *
 */
class SetPlatformId
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
        Auth::user()->platform_id = $request->route()->parameters()['id'];

        return $next($request);
    }
}
