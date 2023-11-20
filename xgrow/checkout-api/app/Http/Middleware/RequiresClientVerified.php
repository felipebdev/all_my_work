<?php

namespace App\Http\Middleware;

use App\Plan;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RequiresClientVerified
{
    public function handle(Request $request, Closure $next)
    {
        $plan = Plan::FindOrFail($request->plan_id);

        $verified = $plan->platform->client->verified ?? false;

        if (!$verified) {
            return response()->json('Plan not available in this moment', Response::HTTP_CONFLICT);
        }

        return $next($request);
    }
}
