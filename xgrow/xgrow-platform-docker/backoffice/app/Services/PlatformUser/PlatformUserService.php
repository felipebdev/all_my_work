<?php

namespace App\Services\PlatformUser;

use App\Repositories\PlatformUserRepository;
use App\Services\Objects\PlatformUserFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class PlatformUserService
{
    protected PlatformUserRepository $platformUser;

    public function __construct(PlatformUserRepository $platformUser)
    {
        $this->platformUser = $platformUser;
    }

    /**
     * @param array $input
     * @return Builder[]|Collection
     */
    public function getUsers(array $input)
    {
        $search = $input['search'] ?? null;
        $status = $input['status'] ?? null;

        $platformUserFilter = (new PlatformUserFilter())
                                    ->setStatus($status)
                                    ->setSearch($search);

        return $this->platformUser->listByPlatform($platformUserFilter)
                                    ->select(
                                        'platforms_users.id as user_id',
                                        'platforms_users.name as user_name',
                                        'platforms_users.email as user_email',
                                        DB::raw('group_concat(platforms.name) as platform_name'),
                                        'platforms_users.active as user_active',
                                        'platforms_users.deleted_at as deleted_at'
                                    )
                                    ->groupBy('platforms_users.id', 'platforms_users.name', 'platforms_users.email', 'platforms_users.active')
                                    ->orderBy('platforms_users.name', 'ASC')
                                    ->get();

    }

    /**
     * Get user
     * @param $id
     * @return mixed
     */
    public function getUser($id)
    {
        return $this->platformUser->findById($id);
    }

    /**
     * Create platform user
     * @param array $data
     * @return mixed
     */
    public function createPlatformUser(array $data)
    {
        return $this->platformUser->create($data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return mixed|object
     */
    public function updatePlatformUser(int $id, array $data)
    {
        return $this->platformUser->update($id, $data);
    }

    /**
     * Delete platform user
     * @param int $id
     * @return void
     */
    public function deletePlatformUser(int $id)
    {
        $this->platformUser->delete($id);
    }

    /**
     * Restore platform user
     * @param $id
     * @return void
     */
    public function restorePlatformUser($id)
    {
        $this->platformUser->restore($id);
    }


}
