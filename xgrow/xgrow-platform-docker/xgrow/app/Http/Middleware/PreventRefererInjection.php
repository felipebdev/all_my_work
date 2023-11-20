<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class PreventRefererInjection
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
        $refererDomain = parse_url($request->header('referer'), PHP_URL_HOST);

        $refererPort = parse_url($request->header('referer'), PHP_URL_PORT);

        if ($refererPort) {
            $refererDomain = "$refererDomain:$refererPort";
        }

        $hostDomain = getallheaders()['Host'];

        if ($refererDomain != $hostDomain) {

            Log::warning('Invalid domain referer', [
                'get_all_headers' => getallheaders(),
                'laravel_headers' => $request->header(),
                'client_ips' => $request->getClientIps(),
            ]);

            App::abort(403, 'Access denied');
        }

        return $next($request);
    }
}
