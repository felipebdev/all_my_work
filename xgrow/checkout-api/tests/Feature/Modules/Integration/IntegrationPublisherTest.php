<?php

namespace Tests\Feature\Modules\Integration;

use App\Subscriber;
use Modules\Integration\Contracts\IQueue;
use Modules\Integration\Enums\EventEnum;
use Modules\Integration\Events\LeadData;
use Modules\Integration\Queue\FakeMQ;
use Modules\Integration\Services\IntegrationPublisher;
use Tests\Feature\Traits\LocalDatabaseIds;
use Tests\TestCase;

//use EnricoStahn\JsonAssert\Assert as JsonAssert;

class IntegrationPublisherTest extends TestCase
{
    use LocalDatabaseIds;

    private IntegrationPublisher $publisher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->swap(IQueue::class, new FakeMQ()); // use a fake MQ instead of BullMQ

        $this->publisher = app()->make(IntegrationPublisher::class);
    }

    public function test_lead_webhook()
    {
        $subscriber = Subscriber::inRandomOrder()->first();
        if (!$subscriber) {
            $this->markTestSkipped('Missing subscriber, skipping');
        }

        $leadData = new LeadData($subscriber);

        $published = $this->publisher->publishIntegrations(
            EventEnum::LEAD_CREATED,
            $this->platformId,
            [$this->salePlanId],
            $leadData
        );

        $this->assertTrue($published->count() > 0);
    }


}
