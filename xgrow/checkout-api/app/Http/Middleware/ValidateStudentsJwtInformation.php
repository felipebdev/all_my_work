<?php

namespace App\Http\Middleware;

use App\Facades\JwtStudentsFacade;
use App\Services\Api\JwtStudentsService;
use App\Subscriber;
use Closure;
use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ValidateStudentsJwtInformation
{
    private $jwtStudentsService;

    public function __construct(JwtStudentsService $jwtStudentsService)
    {
        $this->jwtStudentsService = $jwtStudentsService;
    }

    public function handle($request, Closure $next)
    {
        try {
            $jwt = $request->token ?? $request->bearerToken();

            if (!$jwt) {
                return response()->json('token_absent', 400);
            }

            $payload = $this->jwtStudentsService->decode($jwt);

            if (!isset($payload->email)) {

                return response()->json([
                    "error" => true,
                    'message' => 'Email not assigned to token',
                    'response' => ['Email not assigned to token']
                ], 400);
            }

            if (!isset($payload->subscribers_ids)) {

                return response()->json([
                    "error" => true,
                    'message' => 'Subscribers id not assigned to token',
                    'response' => ['Subscribers id not assigned to token']
                ], 400);
            }

            if (!isset($payload->products_ids)) {

                return response()->json([
                    "error" => true,
                    'message' => 'Products id not assigned to token',
                    'response' => ['Products id not assigned to token']
                ], 400);
            }

            if (!Subscriber::getSubscriber($payload->email)) {

                return response()->json([
                    "error" => true,
                    'message' => 'Email not found',
                    'response' => ['Email not found']
                ], 404);
            }

            JwtStudentsFacade::setPayload($payload);

        } catch (SignatureInvalidException $e) {
            return response()->json('token_invalid', 401);
        } catch (ExpiredException $e) {
            return response()->json('token_expired', 401);
        } catch (ModelNotFoundException $e) {
            return response()->json('subscriber_not_found', 404);
        }

        return $next($request);
    }

}
