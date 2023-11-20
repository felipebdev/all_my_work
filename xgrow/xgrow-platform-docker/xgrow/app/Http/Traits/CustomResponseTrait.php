<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

trait CustomResponseTrait
{
    /**
     * Function for standard json response
     * @param  string  $message
     * @param  int  $status
     * @param  array  $data
     * @return JsonResponse
     */
    public function customJsonResponse(
        string $message = '',
        int $status = 200,
        array $data = [],
        ?Throwable $exception = null
    ): JsonResponse {
        if ($status >= 500) {
            Log::error($message, ['exception' => $exception]);
            if (!is_null($exception)) {
                \Sentry\captureException($exception);
            }
        }

        return response()->json([
            'error' => $status != 200 && $status != 201 && $status != 202 && $status != 204,
            'message' => $message,
            'response' => $data,
        ], $status);
    }
}
