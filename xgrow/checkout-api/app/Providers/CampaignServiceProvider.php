<?php

namespace App\Providers;

use App\Repositories\Campaign\AudienceConditionRepository;
use App\Repositories\Contracts\AudienceConditionInterface;
use App\Services\BulkGate\BulkGateSmsService;
use App\Services\BulkGate\FakeSmsService;
use App\Services\Contracts\SendSmsInterface;
use App\Services\Contracts\SendVoiceInterface;
use App\Services\Zenvia\FakeVoiceService;
use App\Services\Zenvia\ZenviaVoiceService;
use Illuminate\Support\ServiceProvider;

class CampaignServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() == 'production') {
            $this->app->bind(SendSmsInterface::class, BulkGateSmsService::class);
            $this->app->bind(SendVoiceInterface::class, ZenviaVoiceService::class);
        } else {
            $this->app->bind(SendSmsInterface::class, FakeSmsService::class);
            $this->app->bind(SendVoiceInterface::class, FakeVoiceService::class);
        }

        $this->app->bind(AudienceConditionInterface::class, AudienceConditionRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
