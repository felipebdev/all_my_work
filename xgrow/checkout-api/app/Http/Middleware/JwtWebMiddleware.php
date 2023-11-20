<?php

namespace App\Http\Middleware;

use App\Facades\JwtWebFacade;
use App\Http\Middleware\Purposes\JwtPurposeFactory;
use App\Http\Traits\CustomResponseTrait;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class JwtWebMiddleware extends BaseJwtWebValidation
{
    use CustomResponseTrait;

    /**
     * Middleware to check JWT used by Web Platform validating "Purposes".
     * A "Purpose" validates additional JWT payload according to business rules.
     *
     * Usage example on routes:
     * Route::middeware(jwt.web:transfer) // Validates that payload contains consistent data for executing a transfer
     *
     * @param $request
     * @param  \Closure  $next
     * @param $purpose
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function handle($request, Closure $next, $purpose)
    {
        try {
            $this->validateJwt($request);

            $payload = JwtWebFacade::getPayload();

            $purposeValidator = JwtPurposeFactory::getStrategy($purpose);

            $errors = $purposeValidator->getErrors($payload, $request);
            if ($errors) {
                throw new UnauthorizedHttpException('jwt-web', join(', ', $errors));
            }

            return $next($request);
        } catch (InvalidArgumentException | UnauthorizedHttpException $e) {
            return $this->unauthorized($e->getMessage());
        }
    }

    private function unauthorized(string $message): JsonResponse
    {
        return $this->customJsonResponse($message, Response::HTTP_UNAUTHORIZED);
    }

}
