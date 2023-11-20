<?php

namespace App\Http\Controllers\Api\Mobile;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\PlatformUser;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

/**
 *
 */
class AuthenticateController extends Controller
{
    /**
     *
     */
    public function __construct()
    {
        $this->middleware('auth:mobile', ['except' => ['authenticate']]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function authenticate(Request $request): JsonResponse
    {
        // grab credentials from the request
        $credentials = $request->only('email', 'password');

        try {
            // attempt to verify the credentials and create a token for the user
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        $user = auth()->user();

        $user = PlatformUser::with('thumb', 'platforms')->where('id', $user->id)->first();
        // all good so return the token
        return response()->json(compact('token', 'user'));
    }

    // somewhere in your controller

    /**
     * @return JsonResponse
     */
    public function getAuthenticatedUser(): JsonResponse
    {
        try {

            if (!$user = JWTAuth::parseToken()->authenticate()) {

                return response()->json(['user_not_found'], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());
        }

        $user = PlatformUser::with('thumb', 'platforms')->where('id', $user->id)->first();
        // the token is valid and we have found the user via the sub claim
        return response()->json(compact('user'));
    }

    /**
     * @return JsonResponse
     */
    public function refreshToken(): JsonResponse
    {
        if (!$token = JWTAuth::getToken())

            return response()->json(['error', 'token_not_send'], 401);

        try {

            $token = JWTAuth::refresh();
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidExceptio $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());
        }

        return response()->json(compact('token'));
    }

    /**
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }
}
