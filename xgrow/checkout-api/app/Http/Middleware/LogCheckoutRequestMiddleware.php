<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogCheckoutRequestMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $platformId = $request->route('platform_id') ?? null;
        $planId = $request->route('plan_id') ?? null;

        Log::info('Checkout request received', [
            'url' => $request->fullUrl() ?? null,
            'client_ip' => $request->getClientIp() ?? null,
            'client_user_agent' => $request->userAgent() ?? null,
            'platform_id' => $platformId,
            'plan_id' => $planId,
            'request' => $request->all(),
        ]);

        return $next($request);
    }
}
