<?php

namespace App\Providers;

use App\Role;
use App\Platform;
use App\Services\Producer\ProducerService;
use Illuminate\Auth\SessionGuard;
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

        $this->registerSessionGuards();

        if ($this->ignoreGates()) {
            return;
        }

        if ($this->app->isDownForMaintenance()) return;

        Gate::define('producer', function ($user) {
            return Platform::checkProducerPermission($user->platform_id, $user->id);
        });

        $this->registerGatesFromDb();
    }

    private function ignoreGates(): bool
    {
        if (app()->runningInConsole()) {
            return true;
        }

        $skipPath = [
            'api/events/'
        ];

        $path = request()->path();

        if (Str::startsWith($path, $skipPath)) {
            return true;
        }

        return false;
    }

    private function registerGatesFromDb(): void
    {
        foreach (Role::all() as $role) {
            Gate::define($role->slug, function ($user) use ($role) {
                return Platform::checkPermission($user->platform_id, $user->id, $role->slug);
            });
        }
    }

    private function registerSessionGuards(): void
    {
        // Auth::isProducer() or auth()->isProducer()
        SessionGuard::macro('isProducer', function () {
            /** @var \App\User $user */
            $user = auth()->user();

            if ($user === null) {
                return false;
            }

            if ($user->platform_id == null) {
                return false;
            }

            /** @var ProducerService $service */
            $service = resolve(ProducerService::class);
            return $service->isProducerOnPlatform($user->id, $user->platform_id);
        });
    }


}
