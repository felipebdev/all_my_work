<?php

namespace App\Http\Traits;

use Exception;
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
     * @return JsonResponse
     */
    public function customJsonResponse(string $message = '', int $status = 200, array $data = []): JsonResponse
    {
        return response()->json([
            'error' => $status != 200 && $status != 201 && $status != 204,
            'message' => $message,
            'response' => $data
        ], $status);
    }

    /**
     * @param mixed $data
     * @param  string  $message
     * @param  int  $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function successJsonResponse($data, string $message = 'success', int $status = 200): JsonResponse
    {
        return response()->json([
            'error' => $status != 200 && $status != 201 && $status != 204,
            'message' => $message,
            'response' => $data
        ], $status);
    }

    /**
     * Throws an anonymous Renderable Exception with custom response payload
     *
     * @param  string  $message
     * @param  int  $status
     * @param  array  $data
     * @throws \Exception
     */
    public function customAbort(string $message = '', int $status = 400, array $data = [], ?\Throwable $exception = null): void
    {
        $errorException = new class($message, $status, $data, $exception) extends Exception implements DontReportInterface {
            protected $message;
            protected $status;
            protected $data;
            protected $exception;

            public function __construct(string $message = '', int $status = 200, array $data = [], ?\Throwable $exception = null)
            {
                parent::__construct($message, $status);
                $this->message = $message;
                $this->status = $status;
                $this->data = $data;
                $this->exception = $exception;
            }

            public function render($request)
            {
                if ($this->status >= 500) {
                    Log::error('Checkout 500 error',
                        [
                            'data' => $this->data,
                            'message' => $this->message,
                            'reason' => optional($this->exception)->getMessage() ?? null,
                            'code' => optional($this->exception)->getCode() ?? null,
                            'line' => optional($this->exception)->getLine() ?? null,
                            'file' => optional($this->exception)->getFile() ?? null
                        ]);
                }

                return response()->json([
                    'error' => $this->status != 200 && $this->status != 201 && $this->status != 204,
                    'message' => $this->message,
                    'response' => $this->data
                ], $this->status);
            }
        };

        throw $errorException;
    }
}
