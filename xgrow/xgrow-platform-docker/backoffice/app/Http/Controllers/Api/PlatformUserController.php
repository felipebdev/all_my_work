<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CollectionHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\PlatformUser\IndexRequest;
use App\Http\Requests\PlatformUser\PlatformUserStoreRequest;
use App\Http\Requests\PlatformUser\PlatformUserUpdateRequest;
use App\Http\Traits\CustomResponseTrait;
use App\Services\PlatformUser\PlatformUserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class PlatformUserController extends Controller
{
    use CustomResponseTrait;

    private PlatformUserService $platformUserService;

    /**
     * @return void
     */
    public function __construct(
        PlatformUserService $platformUserService
    ) {
        $this->platformUserService = $platformUserService;
    }

    /**
     * List all platform users
     * @param Request $request
     * @return JsonResponse
     */
    public function index(IndexRequest $request)
    {
        try {
            $offset = $request->input('offset') ?? 25;

            $users = $this->platformUserService->getUsers($request->only('search', 'status'));
            $data = ['users' => CollectionHelper::paginate($users, $offset)];

            return $this->customJsonResponse('Dados carregados com sucesso.', 200, $data);
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 400, []);
        }
    }

    /**
     * Show platform users data
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $user = $this->platformUserService->getUser($id);
            $data = ['user' => $user];

            return $this->customJsonResponse('Dados carregados com sucesso.', 200, $data);
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 400);
        }
    }

    /**
     * Save platform users data
     * @param PlatformUserStoreRequest $request
     * @return JsonResponse
     */
    public function store(PlatformUserStoreRequest $request)
    {
        try {
            $user = $this->platformUserService->createPlatformUser($request->all());
            $data = ['user' => $user];

            return $this->customJsonResponse('Usuário adicionado com sucesso.', 201, $data);
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 500, []);
        }
    }

    /**
     * Update platform users data
     * @param PlatformUserUpdateRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(PlatformUserUpdateRequest $request, $id)
    {
        try {
            $user = $this->platformUserService->updatePlatformUser($id, $request->all());
            $data = ['user' => $user];

            return $this->customJsonResponse('Dados atualizados com sucesso.', 200, $data);
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 500, []);
        }
    }

    /**
     * Delete platform user
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            $this->platformUserService->deletePlatformUser($id);
            return $this->customJsonResponse('Usuário removido com sucesso.', 200);
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 400, []);
        }
    }

    /**
     * Delete platform user
     * @param $id
     * @return JsonResponse
     */
    public function restore($id)
    {
        try {
            $this->platformUserService->restorePlatformUser($id);
            return $this->customJsonResponse('Usuário restaurado com sucesso.', 200);
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 400, []);
        }
    }
}
