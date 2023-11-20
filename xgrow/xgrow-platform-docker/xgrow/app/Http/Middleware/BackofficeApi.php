<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;

class BackofficeApi
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $keyDecode = config('backoffice.key');
            $tokenDecoded = JWT::decode($request->bearerToken(), new Key($keyDecode, 'HS256'));
            $user = User::where('email', $tokenDecoded->loggedUser)->first();

            return ($user) ? $next($request) : $this->unauthorizedUser();
        } catch (Exception $e) {
            return $this->unauthorizedUser();
        }
    }

    private function unauthorizedUser()
    {
        return response()->json(['status' => 'Unauthorized user'], 401);
    }

}
