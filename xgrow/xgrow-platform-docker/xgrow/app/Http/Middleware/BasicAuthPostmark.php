<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class BasicAuthPostmark
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
        $basicAuth = explode(':', base64_decode(str_replace('Basic ', '', $request->header()['authorization'][0])));

        $authUser = env('POSTMARK_WEBHOOK_USER');

        $authPass = env('POSTMARK_WEBHOOK_PASS');

        $isNotAuthenticated = ($basicAuth[0] != $authUser || $basicAuth[1] != $authPass);

        if ($isNotAuthenticated) {
            Log::info('HTTP/1.1 401 Authorization Required', $request->header());
            header('HTTP/1.1 401 Authorization Required');
            header('WWW-Authenticate: Basic realm="Access denied"');
            exit;
        }

        return $next($request);
    }
}
