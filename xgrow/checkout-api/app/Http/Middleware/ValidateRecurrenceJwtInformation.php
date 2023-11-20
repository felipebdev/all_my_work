<?php

namespace App\Http\Middleware;

use App\Services\Api\JwtRecurrenceService;
use Closure;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

class ValidateRecurrenceJwtInformation
{
    private JwtRecurrenceService $jwtRecurrenceService;

    public function __construct(JwtRecurrenceService $jwtRecurrenceService)
    {
        $this->jwtRecurrenceService = $jwtRecurrenceService;
    }

    public function handle($request, Closure $next)
    {
        try {
            $jwt = $request->token ?? $request->bearerToken();

            if (!$jwt) {
                return response()->json('token_absent', 400);
            }

            $payload = $this->jwtRecurrenceService->decode($jwt);

            if (!isset($payload->recurrence_id)) {

                return response()->json([
                    "error" => true,
                    'message' => 'Recurrence not assigned to token',
                    'response' => ['Recurrence not assigned to token']
                ], 400);
            }

            $recurrence_id = request()->route()->parameters()['recurrence_id'];
            if ($payload->recurrence_id <> $recurrence_id) {

                return response()->json([
                    "error" => true,
                    'message' => 'Recurrence of the token is not the same as the parameter',
                    'response' => ['Recurrence of the token is not the same as the parameter']
                ], 400);
            }

        } catch (SignatureInvalidException $e) {
            return response()->json('token_invalid', 401);
        } catch (ExpiredException $e) {
            return response()->json('token_expired', 401);
        }

        return $next($request);
    }

}
