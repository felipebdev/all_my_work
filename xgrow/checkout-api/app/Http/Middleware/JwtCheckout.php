<?php

namespace App\Http\Middleware;

use App\Facades\JwtCheckoutFacade;
use App\Services\Contracts\JwtCheckoutServiceInterface;
use Closure;
use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class JwtCheckout
 *
 * Middleware for validating JWT generated from third party application on checkout.
 * "platform_id" and "plan_id" are required in JWT payload
 *
 * @package App\Http\Middleware
 *
 */
class JwtCheckout
{
    private $jwtCheckoutService;

    public function __construct(JwtCheckoutServiceInterface $jwtCheckoutService)
    {
        $this->jwtCheckoutService = $jwtCheckoutService;
    }

    public function handle($request, Closure $next)
    {
        try {
            $jwt = $request->token ?? $request->bearerToken();

            if (!$jwt) {
                return response()->json('token_absent', 400);
            }

            $payload = $this->jwtCheckoutService->decode($jwt);

            $platformId = $payload->platform_id;
            $planId = $payload->plan_id;

            JwtCheckoutFacade::setToken($jwt)->setPlanId($planId)->setPlatformId($platformId);
        } catch (SignatureInvalidException $e) {
            return response()->json('token_invalid', 401);
        } catch (ExpiredException $e) {
            return response()->json('token_expired', 401);
        } catch (ModelNotFoundException $e) {
            return response()->json('plan_not_found', 404);
        } catch (Exception $e) {
            return $this->unauthorizedUser();
        }

        return $next($request);
    }

    private function unauthorizedUser()
    {
        return response()->json('unauthorized_user', 401);
    }

}
