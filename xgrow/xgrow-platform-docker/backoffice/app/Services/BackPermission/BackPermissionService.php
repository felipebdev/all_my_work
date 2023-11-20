<?php

namespace App\Services\BackPermission;

use App\Repositories\BackPermissionRepository;
use App\Services\Objects\BackPermissionFilter;
use Exception;

class BackPermissionService
{

    private BackPermissionRepository $permission;

    /**
     * @param BackPermissionRepository $permission
     */
    public function __construct(BackPermissionRepository $permission)
    {
        $this->permission = $permission;
    }

    public function store(array $data)
    {
        $permissions = $this->convertToRepository($data);
        return $this->permission->create($permissions);
    }

    public function getPermission($id)
    {
        $data = $this->permission->listAll()->select('id', 'name', 'description')
            ->with('scopes', 'grants')
            ->find($id)->toArray();
        return $this->convertToFrontend($data);
    }

    public function getPermissions($inputs)
    {
        $search = $inputs['search'] ?? null;
        $rolesId = $inputs['rolesId'] ?? null;
        $usersId = $inputs['usersId'] ?? null;

        $filter = (new BackPermissionFilter())
                    ->setSearch($search)
                    ->setUsersId($usersId)
                    ->setRolesId($rolesId);

        return $this->permission
                    ->getAll($filter)
                    ->with('roles:id,name')
                    ->with('users')
                    ->select('id', 'name')
                    ->get();
    }

    public function listPermissions()
    {
        return $this->permission->listAll()
                    ->select('id', 'name')->get();
    }

    public function update(int $id, array $data)
    {
        $permissions = $this->convertToRepository($data);
        return $this->permission->update($id, $permissions);
    }

    /**
     * @throws Exception
     */
    public function delete(int $id)
    {
        if (!$this->permission->delete($id)) {
            throw new Exception('Você não pode excluir essa permissão. Há usuários vinculados a ela.');
        }
    }

    /**
     * Convert data to repository patterns
     * @param array $data
     * @return array
     */
    private function convertToRepository(array $data): array
    {
        foreach ($data['permissions'] as $permission) {
            $data['scopes'][] = [
                'back_role_id' => $permission['back_role_id'],
                'type_access' => $permission['type_access']
            ];
            if (isset($permission['actions'])) {
                foreach ($permission['actions'] as $action) {
                    $data['grants'][] = [
                        'back_role_id' => $permission['back_role_id'],
                        'back_action_id' => $action,
                    ];
                }
            }
        }
        unset($data['permissions']);
        return $data;
    }

    private function convertToFrontend(array $data): array
    {
        $permissionKeys = [];
        foreach ($data['scopes'] as $key => $scopes) {
            $permissionKeys[$scopes['back_role_id']] = $key;
            $data['permissions'][] = [
                'back_role_id' => $scopes['back_role_id'],
                'type_access' => $scopes['type_access']
            ];
        }
        foreach ($data['grants'] as $grants) {
            if (isset($permissionKeys[$grants['back_role_id']])) {
                $key = $permissionKeys[$grants['back_role_id']];
                $data['permissions'][$key]['actions'][] = $grants['back_action_id'];
            }
        }
        unset($data['scopes']);
        unset($data['grants']);
        return $data;
    }
}
