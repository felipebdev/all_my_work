<?php

namespace App\Providers;

use App\Facades\JwtCheckoutFacade;
use App\Repositories\Contracts\LeadRepositoryInterface;
use App\Repositories\Contracts\ProducerProductRepositoryInterface;
use App\Repositories\Contracts\ProducerRepositoryInterface;
use App\Repositories\Contracts\SubscriberRepositoryInterface;
use App\Repositories\Leads\LeadCartRepository;
use App\Repositories\Producers\ProducerProductRepository;
use App\Repositories\Producers\ProducerRepository;
use App\Repositories\Subscribers\SubscriberRepository;
use App\Services\Contracts\FacebookPixelServiceInterface;
use App\Services\Contracts\LeadReportServiceInterface;
use App\Services\Contracts\ProducerReportServiceInterface;
use App\Services\Contracts\SubscriberReportServiceInterface;
use App\Services\Integrations\FacebookPixelService;
use App\Services\Reports\LeadReportService;
use App\Services\Reports\ProducerReportService;
use App\Services\Reports\SubscriberReportService;
use Illuminate\Support\ServiceProvider;
use App\Services\Reports\SaleReportService;
use App\Repositories\Payments\PaymentRepository;
use App\Services\Integrations\IntegrationService;
use App\Services\Subscriptions\SubscriptionService;
use App\Services\Contracts\SaleReportServiceInterface;
use App\Services\Contracts\IntegrationServiceInterface;
use App\Repositories\Integrations\IntegrationRepository;
use App\Services\Contracts\SubscriptionServiceInterface;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Repositories\Subscriptions\SubscriptionRepository;
use App\Repositories\Contracts\IntegrationRepositoryInterface;
use App\Repositories\Contracts\RecurrenceRepositoryInterface;
use App\Repositories\Contracts\SubscriptionRepositoryInterface;
use App\Repositories\Payments\RecurrenceRepository;
use App\Services\Contracts\FinancialReportServiceInterface;
use App\Services\Contracts\PaymentServiceInterface;
use App\Services\Payments\PaymentService;
use App\Services\Reports\FinancialReportService;

class InjectionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerRepositories();
        $this->registerServices();
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

    private function registerRepositories() {
        $this->app->bind(IntegrationRepositoryInterface::class, IntegrationRepository::class);
        $this->app->bind(SubscriptionRepositoryInterface::class, SubscriptionRepository::class);
        $this->app->bind(PaymentRepositoryInterface::class, PaymentRepository::class);
        $this->app->bind(SubscriberRepositoryInterface::class, SubscriberRepository::class);
        $this->app->bind(LeadRepositoryInterface::class, LeadCartRepository::class);
        $this->app->bind(RecurrenceRepositoryInterface::class, RecurrenceRepository::class);
        $this->app->bind(ProducerRepositoryInterface::class, ProducerRepository::class);
        $this->app->bind(ProducerProductRepositoryInterface::class, ProducerProductRepository::class);
    }

    private function registerServices() {
        $this->app->bind(IntegrationServiceInterface::class, IntegrationService::class);
        $this->app->bind(SubscriptionServiceInterface::class, SubscriptionService::class);
        $this->app->bind(SaleReportServiceInterface::class, SaleReportService::class);
        $this->app->bind(SubscriberReportServiceInterface::class, SubscriberReportService::class);
        $this->app->bind(LeadReportServiceInterface::class, LeadReportService::class);
        $this->app->bind(FinancialReportServiceInterface::class, FinancialReportService::class);
        $this->app->bind(PaymentServiceInterface::class, PaymentService::class);
        $this->app->bind(ProducerReportServiceInterface::class, ProducerReportService::class);

        $this->app->bind(FacebookPixelServiceInterface::class, function($params) {
            // resolve using platform_id obtained from JWT Checkout
            $platformId = JwtCheckoutFacade::getPlatformId();
            return $this->app->makeWith(FacebookPixelService::class, ['platformId' => $platformId]);
        });
    }
}
