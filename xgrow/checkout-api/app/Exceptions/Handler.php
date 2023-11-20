<?php

namespace App\Exceptions;

use App\Http\Traits\DontReportInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        //$this->reportable(function (\App\Services\Finances\Payment\Exceptions\InvalidOrderException $e){
        //    \Log::error($e->getMessage(), [$e->getPayload()]);
        //});
    }

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Throwable
     */
    public function report(Throwable $exception)
    {
        if ($exception instanceof DontReportInterface) {
            return;
        }

        if ($exception->getCode() >= 500) {
            Log::error('Checkout exception handler 500 error',
                [
                    'reason' => $exception->getMessage(),
                    'code' => $exception->getCode() ?? null,
                    'line' => $exception->getLine() ?? null,
                    'file' => $exception->getFile() ?? null
                ]);
        }

        if (app()->bound('sentry') && $this->shouldReport($exception)) {
            app('sentry')->captureException($exception);
        }
        parent::report($exception);
    }


    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof AuthorizationException) {
            return redirect(route('login'));
        }

        return parent::render($request, $exception);
    }
}
