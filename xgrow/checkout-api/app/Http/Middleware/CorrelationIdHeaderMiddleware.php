<?php

namespace App\Http\Middleware;

use App\Logs\ChargeLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;
use Sentry\State\Scope;

/**
 * This middleware adds a correlation ID header to the request if it doesn't exist.
 *
 * It also adds the correlation ID to Log context
 */
class CorrelationIdHeaderMiddleware
{
    protected static string $headerName = 'X-Correlation-Id';

    public function handle(Request $request, Closure $next)
    {
        if (!$request->headers->has(self::$headerName)) {
            // add correlation id to header if not exits
            $request->headers->set(self::$headerName, (string) Uuid::uuid4());
        }

        $correlationId = $request->headers->get(self::$headerName);

        // add correlation id on log context
        Log::withContext(['correlation_id' => $correlationId]);
        ChargeLog::withContext(['correlation_id' => $correlationId]);

        if (app()->bound('sentry')) {
            // add correlation id as a searchable tag on sentry
            app('sentry')->configureScope(function (Scope $scope) use ($correlationId): void {
                $scope->setTag('correlation_id', $correlationId ?? '');
            });
        }

        return $next($request);
    }

    public static function get(): string
    {
        return request()->header(self::$headerName, '');
    }

    public static function getStart(): string
    {
        return strtok(self::get(), '-');
    }

}
