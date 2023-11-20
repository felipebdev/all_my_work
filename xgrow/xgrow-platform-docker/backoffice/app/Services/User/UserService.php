<?php

namespace App\Services\User;

use App\Repositories\UserRepository;
use App\Services\Objects\UserFilter;

class UserService
{
    /**
     * @var UserRepository
     */
    private UserRepository $user;

    /**
     * @param UserRepository $user
     */
    public function __construct(UserRepository $user)
    {
        $this->user = $user;
    }

    public function getUsers($inputs)
    {
        $search = $inputs['search'] ?? null;
        $status = $inputs['status'] ?? null;

        $filter = (new UserFilter())
            ->setSearch($search)
            ->setStatus($status);

        return $this->user->listAll($filter)->get();
    }

    /**
     * @param $id
     * @return object
     */
    public function getUser($id)
    {
        return $this->user->findById($id);
    }

    /**
     * List users
     * @return object
     */
    public function listUsers()
    {
        return $this->user->listAll()
                    ->select('id', 'name')->get();
    }

    /**
     * @param int $id
     * @return void
     */
    public function deleteUser(int $id)
    {
        $this->user->delete($id);
    }

    /**
     * @param array $data
     * @return object
     */
    public function createUser(array $data)
    {
        return $this->user->create($data);
    }

    /**
     * @param $id
     * @param array $data
     * @return object
     */
    public function updateUser($id, array $data): object
    {
        return $this->user->update($id, $data);
    }

}
