<?php

namespace App\Services\Objects;

class BackPermissionFilter extends BaseFilter
{
    public ?string $search = null;
    public ?array $rolesId = null;
    public ?array $usersId = null;

    public function __construct(
        ?string $search = null,
        ?array $rolesId = null,
        ?array $usersId = null
    ) {
        $this->search = $search;
        $this->rolesId = $rolesId;
        $this->usersId = $usersId;
    }

    /**
     * @param  string|null  $search
     * @return BackPermissionFilter
     */
    public function setSearch(?string $search): BackPermissionFilter
    {
        $this->search = $search;
        return $this;
    }

    /**
     * @param  array|null  $rolesId
     * @return BackPermissionFilter
     */
    public function setRolesId(?array $rolesId): BackPermissionFilter
    {
        $this->rolesId = $rolesId;
        return $this;
    }

    /**
     * @param  array|null  $usersId
     * @return BackPermissionFilter
     */
    public function setUsersId(?array $usersId): BackPermissionFilter
    {
        $this->usersId = $usersId;
        return $this;
    }

}
