<?php

namespace App\Services\Auth;

use Illuminate\Support\Facades\Gate;

class ActionChecker
{
    public const OR_SEPARATOR = '|';

    /**
     * Check if user is authorized (uses "|" as OR operator)
     *
     * @param  string  $action
     * @return bool true if authorized, false otherwise
     */
    public static function authorized(string $action): bool
    {
        $actions = explode(self::OR_SEPARATOR, $action);
        return self::hasAnyAction($actions);
    }

    /**
     * Check if user has ANY of the provided actions.
     *
     * @param  array  $actions
     * @return bool true if has action, false otherwise
     */
    protected static function hasAnyAction(array $actions): bool
    {
        foreach ($actions as $action) {
            if (Gate::allows($action)) {
                return true;
            }
        }

        return false;
    }

}
