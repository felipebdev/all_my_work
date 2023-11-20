<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Log;

class TmbMiddleware
{

    public function handle($request, Closure $next)
    {
        $key = env('TMB_KEY', 'token');
        $secret = env('TMB_TOKEN');

        Log::debug('TMB: request received', [
            'request' => $request->all(),
        ]);

        if ($request->header($key) != $secret) {
            Log::warning('TMB: bad request token', [
                'request' => $request->all(),
            ]);

            throw new AuthorizationException('Sem permissão de acesso');
        }

        Log::warning('TMB: good request', [
            'request' => $request->all(),
        ]);

        return $next($request);
    }
}
