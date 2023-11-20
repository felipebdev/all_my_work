<?php

namespace App\Providers;

use App\BackRole;
use App\BackAction;
use App\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        if ($this->ignoreGates()) {
            return;
        }

        $this->registerGatesFromDb();
    }

    private function ignoreGates(): bool
    {
        if (app()->runningInConsole()) {
            return true;
        }

        $skipPath = [];

        /*
        $skipPath = [
            'api/events/'
        ];
        */

        $path = request()->path();

        if (Str::startsWith($path, $skipPath)) {
            return true;
        }

        return false;
    }

    private function registerGatesFromDb(): void
    {
        foreach (BackRole::all() as $role) {
            Gate::define($role->slug, function ($user) use ($role) {
                return User::checkRole($user->id, $role->id);
            });
            foreach (BackAction::all() as $action) {
                Gate::define("{$role->slug}-{$action->slug}", function ($user) use ($role, $action) {
                    return User::checkAction($user->id, $role->id, $action->id);
                });
            }
        }
    }
}
