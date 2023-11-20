<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CheckoutGoogleRecaptchaV3
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('Content-Rcp');
        $action = $request->header('Content-Act');

        // use the reCAPTCHA PHP client library for validation
        $recaptcha = new \ReCaptcha\ReCaptcha(env("RECAPTCHA_V3_SECRET_KEY"));
        $resp = $recaptcha->setScoreThreshold(env('RECAPTCHA_V3_SCORE', 0.4))
            ->setExpectedAction($action)
            ->verify($token);

        // verify the response
        if ($resp->isSuccess()) {
            return $next($request);
        } else {
            Log::info('[Checkout Api Access] Exception Recaptcha', ['required_score' => env('RECAPTCHA_V3_SCORE', 0.4), 'response_recaptcha' => $resp, 'request' => $request->all(), 'level' => 'info']);
            return response()->json(['message' => 'A sua navegação não está segura. Atualize e tente novamente.'], 403);
        }
    }
}
