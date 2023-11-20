<?php

namespace App\Providers;

use App\Facades\JwtCheckoutFacade;
use App\Repositories\Contracts\LeadRepositoryInterface;
use App\Repositories\Contracts\SubscriberRepositoryInterface;
use App\Repositories\Leads\LeadRepository;
use App\Repositories\Subscribers\SubscriberRepository;
use App\Services\Contracts\FacebookPixelServiceInterface;
use App\Services\Contracts\LeadReportServiceInterface;
use App\Services\Contracts\SubscriberReportServiceInterface;
use App\Services\Integrations\FacebookPixelService;
use App\Services\Reports\LeadReportService;
use App\Services\Reports\SubscriberReportService;
use BulkGate\Sdk\Connection\ConnectionStream;
use BulkGate\Sdk\MessageSender;
use BulkGate\Sdk\Sender;
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
        $this->app->bind(LeadRepositoryInterface::class, LeadRepository::class);
        $this->app->bind(RecurrenceRepositoryInterface::class, RecurrenceRepository::class);
    }

    private function registerServices() {
        $this->app->bind(IntegrationServiceInterface::class, IntegrationService::class);
        $this->app->bind(SubscriptionServiceInterface::class, SubscriptionService::class);
        $this->app->bind(SaleReportServiceInterface::class, SaleReportService::class);
        $this->app->bind(SubscriberReportServiceInterface::class, SubscriberReportService::class);
        $this->app->bind(LeadReportServiceInterface::class, LeadReportService::class);
        $this->app->bind(FinancialReportServiceInterface::class, FinancialReportService::class);

        $this->app->bind(FacebookPixelServiceInterface::class, function($params) {
            // resolve using platform_id obtained from JWT Checkout
            $platformId = JwtCheckoutFacade::getPlatformId();
            return $this->app->make(FacebookPixelService::class, ['platformId' => $platformId]);
        });

        $this->app->bind(Sender::class, function () {
            $connection = new ConnectionStream(
                env('BULKGATE_APPLICATION_ID'),
                env('BULKGATE_APPLICATION_TOKEN')
            );
            return (new MessageSender($connection))->setDefaultCountry('BR');
        });
    }
}
