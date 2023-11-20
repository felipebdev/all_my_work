<?php

namespace Tests\Feature;

use App\Repositories\Contracts\LeadAbandonedCartRepositoryInterface;
use App\Services\Actions\AbandonedCartAction;
use App\Utils\TriggerIntegrationJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\Bus;
use Modules\Integration\Enums\EventEnum;
use Modules\Integration\Queue\Jobs\HandleIntegration;
use Tests\Feature\Traits\CreateSubscriberTrait;
use Tests\Feature\Traits\LocalDatabaseIds;
use Tests\TestCase;

class AbandonedCartActionTest extends TestCase
{
    use TriggerIntegrationJob;
    use LocalDatabaseIds;
    use CreateSubscriberTrait;

    public function test_assert_dispatches_abandoned_cart()
    {
        $this->withoutMiddleware();

        // create one new Lead
        $this->createSubscriber($this->platformId, $this->salePlanId);

        Carbon::setTestNow(Carbon::now()->addMinutes(LeadAbandonedCartRepositoryInterface::DEFAULT_ABANDONED_CART_MINUTES + 1));

        // enable fake bus
        Bus::fake();

        $action = new AbandonedCartAction();
        $affected = $action();

        // assert that at least one Lead was affected
        $this->assertGreaterThanOrEqual(1, count($affected));

        // assert integration dispatch
        Bus::assertDispatched(HandleIntegration::class, function (HandleIntegration $integration) {
            return $integration->event === EventEnum::CART_ABANDONED;
        });
    }

    public function test_assert_not_dispatches_abandoned_cart()
    {
        $this->withoutMiddleware();

        // create one new Lead
        $this->createSubscriber($this->platformId, $this->salePlanId);

        Carbon::setTestNow(Carbon::now()->addMinutes(LeadAbandonedCartRepositoryInterface::DEFAULT_ABANDONED_CART_MINUTES - 1));

        // enable fake bus
        Bus::fake();

        $action = new AbandonedCartAction();
        $action();

        // assert that abandoned cart were NOT dispatched yet
        Bus::assertNotDispatched(HandleIntegration::class, function (HandleIntegration $integration) {
            return $integration->event === EventEnum::CART_ABANDONED;
        });
    }

}
