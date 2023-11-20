<?php

namespace App\Http\Controllers\Api;

use App\Data\Intl;
use App\Http\Controllers\Controller;
use App\Jobs\SendRandomPasswordJob;
use App\Notifications\TwoFactorCode;
use App\Services\BackPermission\BackPermissionService;
use Illuminate\Http\Request;
use App\User;
use App\Http\Requests\Profile\ProfileUpdateRequest;
use App\Http\Traits\CustomResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AuthApiController extends Controller
{
    use CustomResponseTrait;

    /**
     * @var BackPermissionService
     */
    private BackPermissionService $permissionService;

    /**
     * @param BackPermissionService $permissionService
     */

    public function __construct(BackPermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
        Intl::ptBR();
    }

    /**
     * Get bearer token
     * @param Request $request
     * @return JsonResponse
     */
    public function authenticate(Request $request): JsonResponse
    {

        if ($request->user === null || $request->password === null) {
            return $this->customJsonResponse('Campos usuário e senha são obrigatórios.', 400, []);
        }

        try {

            $user = User::where('email', $request->user)->first();

            if (!$user || (!$token = auth('api')->attempt(['email' => $request->user, 'password' => $request->password]))) {
                return $this->customJsonResponse('Usuário ou senha incorreta!', 400, []);
            } else if (!$user->active) {
                return $this->customJsonResponse('Usuário inativo!', 400, []);
            } else if ($user->two_factor_enabled) {
                if (!$request->input('two_factor_code')) {
                    $user->generateTwoFactorCode(24 * 60 * 60); //1 day
                    $user->notify(new TwoFactorCode());
                    return $this->customJsonResponse('Código de verificação enviado', 200, []);
                } else if ($user->isTwoFactorCodeExpired()) {
                    return $this->customJsonResponse('Código de verificação expirado', 400, []);
                } else if ($request->input('two_factor_code') !== $user->two_factor_code) {
                    return $this->customJsonResponse('Código de verificação inválido', 400, []);
                }
                $user->resetTwoFactorCode();
            }

            if ($user->type_access == 'restrict' and $user->back_permission_id) {
                $user->permission = $this->permissionService->getPermission($user->back_permission_id);
            }

            return $this->customJsonResponse('Ok', 200, ['user' => $user->toArray(), 'token' => $token]);
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 400, []);
        }
    }

    /**
     * Send new random password
     * @param Request $request
     * @return JsonResponse
     */
    public function newRandomPassword(Request $request): JsonResponse
    {
        $user = User::whereEmail($request->input('email'))->first();

        if (!$user)
            return $this->customJsonResponse('E-mail não localizado!', 400, []);

        $password = randomPassword();
        $user->password = bcrypt($password);
        $user->save();

        dispatch(new SendRandomPasswordJob([
            'user_email' => $user->email,
            'user_name' => $user->name,
            'password' => $password
        ]));

        return $this->customJsonResponse('E-mail enviado com sucesso!', 200, []);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }

    protected function tokenValidate()
    {
        $user = auth('api')->user();
        if ($user['type_access'] == 'restrict' and $user['back_permission_id']) {
            $user['permission'] = $this->permissionService->getPermission($user['back_permission_id']);
        }
        return $this->customJsonResponse('Ok', 200, ['user' => $user]);
    }

    protected function tokenLogout()
    {
        auth('api')->logout();
        return $this->customJsonResponse('Ok', 200, []);
    }

    /**
     * Update user profile
     * @return JsonResponse
     * @throws BindingResolutionException
     */
    protected function updateProfile(ProfileUpdateRequest $request)
    {
        $user = auth('api')->user();
        $user = User::findOrFail($user->id);
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->two_factor_enabled = $request->input('two_factor_enabled');
        if ($request->input('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return $this->customJsonResponse('Dados atualizado com sucesso!', 200, ['user' => $user]);
    }

    /**
     * Get active logged user
     * @return JsonResponse
     * @throws BindingResolutionException
     */
    public function getProfile()
    {
        $user = auth('api')->user();
        $user = User::findOrFail($user->id);

        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'two_factor_enabled' => $user->two_factor_enabled,
        ];
        return $this->customJsonResponse('Ok', 200, ['user' => $data]);
    }
}
