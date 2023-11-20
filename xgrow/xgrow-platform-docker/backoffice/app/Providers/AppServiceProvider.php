<?php

namespace App\Providers;

use App\Services\CarouselService;
use App\Services\NavbarMessageService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Services\NavbarNotificationService;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Http;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(UrlGenerator $url)
    {
        Schema::defaultStringLength(191);
        if (env('APP_ENV') !== 'local') {
            $this->app['request']->server->set('HTTPS', true);
            $url->forceScheme('https');
        }

        Http::macro('learningArea', function () {
            return Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'x-platform-xgrow' => config('services.learninarea.intense.token'),
            ])->baseUrl(config('services.learninarea.intense.url'));
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
    }
}
