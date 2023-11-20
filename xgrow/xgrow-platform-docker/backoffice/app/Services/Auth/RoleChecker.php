<?php

namespace App\Services\Auth;

use Illuminate\Support\Facades\Gate;

class RoleChecker
{
    public const OR_SEPARATOR = '|';

    /**
     * Check if user is authorized (uses "|" as OR operator)
     *
     * @param  string  $role
     * @return bool true if authorized, false otherwise
     */
    public static function authorized(string $role): bool
    {
        $roles = explode(self::OR_SEPARATOR, $role);
        return self::hasAnyRole($roles);
    }

    /**
     * Check if user has ANY of the provided roles.
     *
     * @param  array  $roles
     * @return bool true if has role, false otherwise
     */
    protected static function hasAnyRole(array $roles): bool
    {
        foreach ($roles as $role) {
            if (Gate::allows($role)) {
                return true;
            }
        }

        return false;
    }

}
