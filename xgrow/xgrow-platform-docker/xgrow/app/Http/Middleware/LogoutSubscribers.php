<?php

namespace App\Http\Middleware;

use Closure;

class LogoutSubscribers
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
//        $user = auth('api')->user();
//
//        if ($user !== null ) {
//
//            if ($user->markedForLogout() === 1) {
//
//                $user->unmarkForLogout();
//                $user->save();
//
//                auth('api')->logout();
//
//                return response()->json(['logout' => 'true']);
//            }
//
//            return $next($request);
//
//        } elseif (isset($request->route) && ($request->route === "login" || $request->route === "logout")) {
//
//            return $next($request);
//        }
//
//        return response()->json(['logout' => 'true']);
        return $next($request);

    }
}
