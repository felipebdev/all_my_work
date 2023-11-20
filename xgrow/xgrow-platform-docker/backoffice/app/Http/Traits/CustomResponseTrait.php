<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

trait CustomResponseTrait
{
    /**
     * Function for standard json response
     * @param string $message
     * @param int $status
     * @param array $data
     * @param Throwable|null $exception
     * @return JsonResponse
     */
    public function customJsonResponse(string $message = '', int $status = 200, array $data = [], ?Throwable $exception = null): JsonResponse
    {
        return response()->json([
            'error' => $status != 200 && $status != 201 && $status != 204,
            'message' => $message,
            'response' => $data
        ], $status);
    }
}
