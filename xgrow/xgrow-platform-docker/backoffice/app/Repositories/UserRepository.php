<?php

namespace App\Repositories;

use App\User;
use App\Services\Objects\UserFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class UserRepository
{

    /**
     * Get Users
     * @param UserFilter|null $filter
     * @return Builder
     */
    public function listAll(?UserFilter $filter = null): Builder{
        return  User::when($filter,function ($query, $filter) {
            return  User::when($filter->search, function ($query, $search) {
                $query->where('users.name', 'LIKE', "%{$search}%");
                $query->orWhere('users.email', 'LIKE', "%{$search}%");
            })
                ->when(isset($filter->status), function ($query) use($filter){
                    $query->where('users.active', $filter->status);
                });
        });
    }

    /**
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $client = User::findOrFail($id);
        return $client->delete($id);
    }

    /**
     * @param int $id
     * @return object
     */
    public function findById(int $id): object
    {
        return User::findOrFail($id);
    }

    /**
     * @param array $data
     * @return object
     */
    public function create(array $data): object
    {
        $data['password'] = Hash::make($data['password']);
        $model = (new User())->newInstance($data);
        $model->save();
        return $model;
    }

    /**
     * @param $id
     * @param array $data
     * @return object
     */
    public function update($id, array $data): object
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        $model = User::findOrFail($id);
        $model->fill($data);
        $model->save();
        return $model;
    }
}
