<?php

namespace App\Repositories;

use App\Services\Objects\PlatformUserFilter;
use App\PlatformUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;


class PlatformUserRepository
{

    /**
     * Get PlatformUsers
     * @param PlatformUserFilter $filter
     * @return Builder
     */
    public function listAll(PlatformUserFilter $filter): Builder
    {

        return PlatformUser::when($filter->search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('platforms_users.name', 'LIKE', "%{$search}%");
                $q->orWhere('platforms_users.email', 'LIKE', "%{$search}%");
            });
        })
            ->when($filter->platformId, function ($query, $platformId) {
                $query->where('platforms_users.status', '=', $platformId);
            })
            ->when($filter->createdPeriod, function ($query, $periodFilter) {
                $query->whereBetween('platforms_users.created_at', [$periodFilter->startDate, $periodFilter->endDate]);
            })
            ->when($filter->status, function ($query, $status) {
                if ($status === 'inactive')
                    $query->onlyTrashed();
                elseif ($status === 'both')
                    $query->withTrashed();
            });
    }

    /**
     * List platform user by platform
     * @param PlatformUserFilter $filter
     * @return Builder
     */
    public function listByPlatform(PlatformUserFilter $filter): Builder
    {
        return $this->listAll($filter)
            ->leftJoin('platform_user', 'platform_user.platforms_users_id', '=', 'platforms_users.id')
            ->leftJoin('platforms', 'platforms.id', '=', 'platform_user.platform_id');
    }

    /**
     * Get Platform User by ID
     *
     * @param int $id
     * @return mixed
     */
    public function findById(int $id)
    {
        return PlatformUser::findOrFail($id)
            ->load('platforms:id,name');
    }

    /**
     * Save Platform User
     *
     * @param $request
     * @return mixed
     */
    public function create($request)
    {

        $user_id = PlatformUser::insertGetId([
            'platform_id' => $request['platforms'][0],
            'active' => $request['active'] ?? 1,
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        foreach ($request['platforms'] as $platform) {
            DB::table('platform_user')->insert([
                'platform_id' => $platform,
                'platforms_users_id' => $user_id,
                'type_access' => $request['accessPermition'],
            ]);
        }

        return $this->findById($user_id);
    }

    /**
     * Update Platform User
     *
     * @param int $id
     * @param array $data
     * @return mixed
     */
    public function update(int $id, array $data): object
    {

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        $data['updated_at'] = Carbon::now();

        $platform = PlatformUser::findOrFail($id);
        $platform->fill($data);
        $platform->save();

        if (isset($data['platforms'])) {
            foreach ($data['platforms'] as $platform_id) {
                $type_access = 'full';
                $permission_id = null;
                $plaform_user = DB::table('platform_user')
                    ->where('platform_id', $platform_id)
                    ->where('platforms_users_id', $id)
                    ->first();
                if ($plaform_user) {
                    $type_access = $plaform_user->type_access;
                    $permission_id = $plaform_user->permission_id;
                }
                $platforms[] = [
                    'platform_id' => $platform_id,
                    'platforms_users_id' => $id,
                    'type_access' => $type_access,
                    'permission_id' => $permission_id
                ];
            }

            $platform->platforms()->sync([]); //avoid error sync
            $platform->platforms()->sync($platforms);
        }

        return $this->findById($id);
    }

    /**
     * Delete platform user
     * @param int $id
     * @return void
     */
    public function delete(int $id)
    {
        $platform = PlatformUser::findOrFail($id);
        $platform->delete(); //soft delete
    }

    /**
     * Restore platform user
     * @param int $id
     * @return void
     */
    public function restore($id)
    {
        $platform = PlatformUser::onlyTrashed()->findOrFail($id);
        $platform->restore();
    }
}
