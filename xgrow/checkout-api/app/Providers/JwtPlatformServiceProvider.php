<?php

namespace App\Providers;

use App;
use App\Services\Api\JwtCheckoutService;
use App\Services\Api\JwtPlatformService;
use App\Services\Contracts\JwtCheckoutServiceInterface;
use App\Services\Contracts\JwtPlatformServiceInterface;
use Illuminate\Support\ServiceProvider;

class JwtPlatformServiceProvider extends ServiceProvider
{
    public function boot()
    {
        //
    }

    public function register()
    {
        // register JWT used in Checkout
        $this->app->singleton('jwtcheckout', function () {
            return new App\Helpers\JwtCheckoutHelper();
        });

        $this->app->bind(JwtCheckoutServiceInterface::class, JwtCheckoutService::class);

        $this->app->singleton('jwtplatform', function () {
            return new App\Helpers\JwtPlatformHelper();
        });

        $this->app->bind(JwtPlatformServiceInterface::class, JwtPlatformService::class);

        // register JWT helper used by Web Platform
        $this->app->singleton('jwtweb', function () {
            return new App\Helpers\JwtWebHelper();
        });
    }
}
