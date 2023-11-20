<?php

namespace App\Exceptions;

use Exception;
use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

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
     * @param \Throwable $exception
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
	   // if($this->shouldntReport($exception) && app()->bound('sentry')) app('sentry')->captureException($exception);
	    parent::report($exception);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \Throwable $exception
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function render($request, Throwable $exception)
    {
        return parent::render($request, $exception);
    }

	public function register():void
	{
		$this->reportable(function (Throwable $exception)
		{
			$this->capture($exception);
		});
	}
	private function capture(Throwable $exception)
	{
		if ($this->shouldReport($exception) && app()->bound('sentry')) app('sentry')->captureException($exception);
	}
}
