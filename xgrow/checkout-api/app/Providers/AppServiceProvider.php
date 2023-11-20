<?php

namespace App\Providers;

use App\Helpers\AccessLogHelper;
use App\Services\CarouselService;
use App\Services\NavbarMessageService;
use App\Services\SeedTemplateService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
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

        RateLimiter::for('SendSmsAccessDataJob', function ($job) {
            return Limit::perMinute(30);
        });
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

        $this->app->singleton('accesslog', function () {
            return new AccessLogHelper();
        });

              //check that app is local
       if ($this->app->isLocal()) {
            //if local register your services you require for development
            //$this->app->register('Barryvdh\Debugbar\ServiceProvider');
        }else{
              //else register your services you require for production
            $this->app['request']->server->set('HTTPS', true);
        }
    }
}
