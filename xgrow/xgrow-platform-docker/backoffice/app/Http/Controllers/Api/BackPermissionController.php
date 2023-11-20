<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CollectionHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\{BackPermission\IndexRequest, BackPermission\StoreUpdateRequest};
use App\Http\Traits\CustomResponseTrait;
use App\Services\BackPermission\BackPermissionService;
use Illuminate\Http\JsonResponse;

class BackPermissionController extends Controller
{
    use CustomResponseTrait;

    /**
     * @var BackPermissionService
     */
    private BackPermissionService $permissionService;

    /**
     * @param BackPermissionService $permissionService
     */
    public function __construct(BackPermissionService $permissionService){
        $this->permissionService = $permissionService;
    }


    /**
     * Get permissions
     * @param IndexRequest $request
     * @return JsonResponse
     */
    public function index(IndexRequest $request): JsonResponse
    {
        try {
            $offset = $request->offset ?? 25;
            $permissions = $this->permissionService->getPermissions(
                $request->only('search', 'rolesId', 'usersId')
            );

            return $this->customJsonResponse(
                'Dados carregados com sucesso.',
                200,
                [
                    'permissions' => CollectionHelper::paginate($permissions, $offset)
                ]
            );
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 500, []);
        }
    }

    /**
     * Show permission data
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $permission = $this->permissionService->getPermission($id);
            return $this->customJsonResponse(
                'Dados da permissão.',
                200,
                ['permission' => $permission]
            );
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }

    /**
     * Store permissions
     *
     * @param StoreUpdateRequest $request
     * @return JsonResponse
     */
    public function store(StoreUpdateRequest $request)
    {
        try {
            $permission = $this->permissionService->store($request->only('name', 'description', 'permissions'));
            return $this->customJsonResponse('Permissão cadastrada com sucesso.', 201, ['permission' => $permission]);
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 500, []);
        }
    }

    /**
     * Update the specified permission.
     *
     * @param StoreUpdateRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(StoreUpdateRequest $request, int $id): JsonResponse
    {
        try {
            $permissions = $this->permissionService->update($id, $request->all());
            return $this->customJsonResponse('Dados atualizados com sucesso.', 200, ['permission' => $permissions]);
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 500, []);
        }
    }

    /**
     * Remove the specified permission.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->permissionService->delete($id);
            return $this->customJsonResponse(
                'Permissão excluída com sucesso.',
                200,
                []
            );
        } catch (\Exception $exception) {
            return $this->customJsonResponse($exception->getMessage(), 500, []);
        }
    }

    /**
     * list permissions
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        try {
            $permissions = $this->permissionService->listPermissions();
            return $this->customJsonResponse(
                'Dados carregados com sucesso.',
                200,
                ['permissions' => $permissions]
            );
        } catch (Exception $e) {
            return $this->customJsonResponse($e->getMessage(), 400, []);
        }
    }
}
