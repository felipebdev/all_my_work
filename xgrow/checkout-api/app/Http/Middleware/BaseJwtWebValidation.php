<?php

namespace App\Http\Middleware;

use App\Facades\JwtWebFacade;
use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Http\Request;
use stdClass;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Class BaseJwtWebValidation
 *
 * Base Middleware for validating JWT generated from Web Platform.
 * This base validates ONLY JWT integrity (signature), every concrete middleware must validate against payload.
 *
 * @package App\Http\Middleware
 */
class BaseJwtWebValidation
{
    public $defaultJwtAlgorithm = 'HS256';

    private $key;

    public function __construct()
    {
        $this->key = config('jwtplatform.jwt_web');
    }

    public function validateJwt(Request $request)
    {
        $token = $this->getToken($request);
        $payload = $this->decodeToken($token);
        $this->acceptPayload($payload);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return string
     * @throw UnauthorizedHttpException
     */
    protected function getToken(Request $request): string
    {
        $jwt = $request->token ?? $request->bearerToken();

        if (!$jwt) {
            throw new UnauthorizedHttpException('jwt-web', 'Token absent');
        }

        return $jwt;
    }

    /**
     * Decode JWT token
     *
     * @param  string  $jwt
     * @return \stdClass Returns decoded payload
     */
    protected function decodeToken(string $jwt): stdClass
    {
        try {
            return JWT::decode($jwt, $this->key, [$this->defaultJwtAlgorithm]);
        } catch (SignatureInvalidException $e) {
            throw new UnauthorizedHttpException('jwt-web', 'Token invalid');
        } catch (ExpiredException $e) {
            throw new UnauthorizedHttpException('jwt-web', 'Token expired');
        } catch (Exception $e) {
            throw new UnauthorizedHttpException('jwt-web', 'Unauthorized user');
        }
    }

    protected function acceptPayload($payload): self
    {
        JwtWebFacade::setPayload($payload);
        return $this;
    }

}
