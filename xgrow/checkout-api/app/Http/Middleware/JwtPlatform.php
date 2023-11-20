<?php

namespace App\Http\Middleware;

use App\Facades\JwtPlatformFacade;
use App\Services\Api\JwtPlatformService;
use App\Subscriber;
use Carbon\Carbon;
use Closure;
use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

/**
 * Class JwtPlatform
 *
 * Middleware for validating JWT generated from third party applications.
 * "platform_id" is required in JWT payload
 *
 * @package App\Http\Middleware
 *
 */
class JwtPlatform
{
    private $jwtPlatformService;

    public function __construct(JwtPlatformService $jwtPlatformService)
    {
        $this->jwtPlatformService = $jwtPlatformService;
    }

    public function handle($request, Closure $next)
    {
        try {
            $jwt = $request->token ?? $request->bearerToken();

            if (!$jwt) {
                return response()->json('token_absent', 400);
            }

            $payload = $this->jwtPlatformService->decode($jwt);

            $documentNumber = $payload->document_number;
            $email = $payload->email;
            $platformId = $payload->platform_id;

            $strippedDocument = $this->cleanupDocumentNumber($documentNumber);

            $sqlStripDocument = 'REPLACE(REPLACE(REPLACE(document_number, ".", ""), "-", ""), "/", "")';

            $subscriber = Subscriber::whereRaw("$sqlStripDocument = ? ", $strippedDocument)
                ->where('email', $email)
                ->where('platform_id', $platformId)
                ->firstOrFail();

            JwtPlatformFacade::setToken($jwt)->setSubscriber($subscriber)->setPlatformId($platformId);

            // Last access
            $subscriber->update(['last_acess' => Carbon::now('America/Sao_Paulo')]);
        } catch (SignatureInvalidException $e) {
            return response()->json('token_invalid', 401);
        } catch (ExpiredException $e) {
            return response()->json('token_expired', 401);
        } catch (Exception $e) {
            return $this->unauthorizedUser();
        }

        return $next($request);
    }

    private function cleanupDocumentNumber($documentNumber)
    {
        return preg_replace('/[^0-9]/', '', $documentNumber);
    }

    private function unauthorizedUser()
    {
        return response()->json('unauthorized_user', 401);
    }

}
