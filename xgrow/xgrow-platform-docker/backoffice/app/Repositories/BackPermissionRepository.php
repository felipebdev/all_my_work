<?php

namespace App\Repositories;

use App\BackPermission;
use App\Services\Objects\BackPermissionFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BackPermissionRepository
{
    /**
     * @param BackPermissionFilter|null $filter
     * @return Builder
     */
    public function listAll(?BackPermissionFilter $filter = null): Builder{
        return  BackPermission::when($filter,function ($query, $filter) {
            return  BackPermission::when($filter->search,function ($query, $search) {
                return $this->permissionFilter($query, $search);
            });
        });
    }

    public function permissionFilter($query, $search) {
        return $query->where( function ($q) use ($search) {
            $q->where('back_permissions.name', 'LIKE', "%{$search}%");
            $q->orWhere('back_permissions.description', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Get Permissions
     * @param BackPermissionFilter $filter
     * @return mixed
     */
    public function getAll(BackPermissionFilter $filter)
    {
        return BackPermission::when($filter->search, function ($query, $search) {
                    return $this->permissionFilter($query, $search);
                })
                ->when($filter->rolesId, function ($query, $rolesId) {
                    return $query->whereHas('roles', function($query) use ($rolesId){
                        $query->whereIn('id', $rolesId);
                    });
                })
                ->when($filter->usersId, function ($query, $usersId) {
                    return $query->whereHas('users', function($query) use ($usersId){
                        $query->whereIn('id', $usersId);
                    });
                });
    }

    /**
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model{
        $permission = (new BackPermission())->newInstance($data);
        $permission->save();
        if(isset($data['scopes'])){
            $permission->roles()->sync($data['scopes']); //scope
            if(isset($data['grants']))
                $permission->actions()->sync($data['grants']); //grants
        }
        return $permission;
    }

    /**
     * @param int $id
     * @param array $data
     * @return Model
     */
    public function update(int $id, array $data)
    {
        $permission = BackPermission::findOrFail($id);
        $permission->fill($data);
        $permission->save();
        $permission->roles()->sync([]); //avoid sync error
        $permission->actions()->sync([]); //avoid sync error
        if(isset($data['scopes'])){
            $permission->roles()->sync($data['scopes']);
            if(isset($data['grants']))
                $permission->actions()->sync($data['grants']);
        }
        return $permission;
    }

    /**
     * @param int $id
     * @return mixed|null
     */
    public function delete(int $id)
    {
        $permission = BackPermission::findOrFail($id);
        if (!$permission->users()->count()){
            $permission->roles()->sync([]);
            $permission->actions()->sync([]);
            return $permission->forceDelete();
        }
        return null;
    }

}
