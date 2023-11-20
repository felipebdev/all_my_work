<?php

namespace Modules\Integration\Providers;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Modules\Integration\Commands\OAuthRefreshTokenCommand;
use Modules\Integration\Contracts\IActionRepository;
use Modules\Integration\Contracts\IActionService;
use Modules\Integration\Contracts\IAppIntegrationRepository;
use Modules\Integration\Contracts\IAppIntegrationService;
use Modules\Integration\Contracts\ILogRepository;
use Modules\Integration\Contracts\ILogService;
use Modules\Integration\Contracts\IOAuthService;
use Modules\Integration\Contracts\IQueue;
use Modules\Integration\Queue\BullMQ;
use Modules\Integration\Repositories\ActionRepository;
use Modules\Integration\Repositories\AppIntegrationRepository;
use Modules\Integration\Repositories\LogMongoRepository;
use Modules\Integration\Services\ActionService;
use Modules\Integration\Services\AppIntegrationService;
use Modules\Integration\Services\LogService;
use Modules\Integration\Services\OAuthService;

class IntegrationServiceProvider extends BaseServiceProvider
{
    const MODULE = 'apps';

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes.php');
        $this->loadViewsFrom(__DIR__ . '/../Views', self::MODULE);
        $this->mergeConfigFrom(__DIR__.'/../config.php', self::MODULE);
        $this->loadTranslationsFrom(__DIR__.'/../Lang', self::MODULE);
        $this->commands([OAuthRefreshTokenCommand::class]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(IAppIntegrationRepository::class, AppIntegrationRepository::class);
        $this->app->bind(IActionRepository::class, ActionRepository::class);
        $this->app->bind(IAppIntegrationService::class, AppIntegrationService::class);
        $this->app->bind(IActionService::class, ActionService::class);
        $this->app->bind(IQueue::class, BullMQ::class);
        $this->app->bind(IOAuthService::class, OAuthService::class);
        $this->app->bind(ILogService::class, LogService::class);
        $this->app->bind(ILogRepository::class, LogMongoRepository::class);
    }
}
