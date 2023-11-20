<?php

namespace App\Providers;

use App\Helpers\AccessLogHelper;
use App\Helpers\FeatureFlagHelper;
use App\Observers\PlatformUserObserver;
use App\PlatformUser;
use App\Services\CarouselService;
use App\Services\NavbarMessageService;
use App\Services\SeedTemplateService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Services\NavbarNotificationService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        PlatformUser::observe(PlatformUserObserver::class);

        $this->bootRegisterSqlLogs();

        $this->bootMacros();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('navbar.messages', NavbarMessageService::class);

        $this->app->singleton('navbar.notifications', NavbarNotificationService::class);

        $this->app->singleton('carousel', CarouselService::class);

        $this->app->singleton('seed.template', SeedTemplateService::class);

        $this->app->singleton('featureflag', function () {
            return new FeatureFlagHelper();
        });

        $this->app->singleton('accesslog', function () {
            return new AccessLogHelper();
        });

        //check that app is local
        if ($this->app->isLocal()) {
            //if local register your services you require for development
            //$this->app->register('Barryvdh\Debugbar\ServiceProvider');
        } else {
            //else register your services you require for production
            $this->app['request']->server->set('HTTPS', true);
        }
    }

    /**
     * Allows use of flag (X-Register-Sql-Logs = '1') via cookie or header for SQL debug purposes
     */
    private function bootRegisterSqlLogs(): void
    {
        $usingCookie = $this->app->request->cookie('X-Register-Sql-Logs');
        if (!is_null($usingCookie)) {
            // Unencrypted cookie values are removed by Laravel on middlewares
            // So, we copy data from cookie to header before it happens
            $this->app->request->headers->set('X-Register-Sql-Logs', $usingCookie);
        }

        // Now our application also accepts flag from header
        $usingHeader = $this->app->request->headers->get('X-Register-Sql-Logs');
        if (!$usingHeader) {
            return;
        }

        DB::listen(function ($query) {
            Log::debug('X-Register-Sql-Logs', [
                'X-Register-Sql-Logs' => true,
                'hostname' => gethostname(),
                'sql' => $query->sql,
                'bindings' => $query->bindings,
                'query' => $query->time,
            ]);
        });
    }

    private function bootMacros(): void
    {
        /**
         * Rename array keys using an array map, eg:
         *
         * Arr::renameKeyIfExists(['one' => 1], ['one' => 'uno']);
         *
         * outputs ['uno' => 1]
         */
        Arr::macro('remapKeyIfExists', function ($array, $map) {
            $result = [];
            foreach ($array as $key => $value) {
                $mapping = $map[$key] ?? $key;
                $result[$mapping] = $value;
            }

            return $result;
        });
    }
}
