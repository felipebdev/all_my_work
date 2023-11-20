<?php

namespace Tests\Feature\Modules\Integration;

use App\Lead;
use App\Utils\TriggerIntegrationJob;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\AssertableJsonString;
use Modules\Integration\Contracts\IQueue;
use Modules\Integration\Enums\EventEnum;
use Modules\Integration\Events\NewLeadData;
use Modules\Integration\Queue\FakeMQ;
use Modules\Integration\Services\IntegrationPublisher;
use Tests\Feature\Traits\CreateSubscriberTrait;
use Tests\Feature\Traits\LocalDatabaseIds;
use Tests\TestCase;

class IntegrationPublisherAbandonedCartTest extends TestCase
{

    use DatabaseTransactions;

    use TriggerIntegrationJob;
    use LocalDatabaseIds;
    use CreateSubscriberTrait;

    private IntegrationPublisher $integrationPublisher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->swap(IQueue::class, new FakeMQ()); // use a fake MQ instead of BullMQ

        $this->integrationPublisher = $this->app->make(IntegrationPublisher::class);
    }

    public function test_assert_abandoned_cart_payload()
    {
        $this->withoutMiddleware();

        // create at least one Lead
        $subscriberId = $this->createSubscriber($this->platformId, $this->salePlanId);

        $newLeadData = new NewLeadData(Lead::inRandomOrder()->first());

        $result = $this->integrationPublisher->publishIntegrations(
            EventEnum::CART_ABANDONED,
            $this->platformId,
            [$this->salePlanId],
            $newLeadData
        );

        $first = $result[0] ?? null;

        // assert integration payload

        $this->assertIsString($first);

        (new AssertableJsonString($first))->assertStructure([
            'header' => [
                'date',
                'app' => [
                    'id',
                    'app_id',
                    'platform_id',
                    'event',
                    'action',
                    'metadata' => [
                        'days_never_accessed',
                    ],
                    'planIds' => [
                    ],
                    'integration' => [
                        'id',
                        'type',
                        'api_key',
                        'api_account',
                        'api_webhook',
                        'api_secret',
                        'metadata',
                    ]
                ]
            ],
            'payload' => [
                'data' => [
                    'subscriber_id',
                    'subscriber_name',
                    'subscriber_email',
                    'subscriber_phone',
                    'subscriber_document_type',
                    'subscriber_document_number',
                    'subscriber_zipcode',
                    'subscriber_street',
                    'subscriber_number',
                    'subscriber_comp',
                    'subscriber_district',
                    'subscriber_city',
                    'subscriber_state',
                    'subscriber_country',
                    'subscriber_plan_id',
                    'plan' => [
                        'id',
                        'plan',
                        'type',
                        'price',
                        'price_plus_fees',
                    ],
                    'product' => [
                        'id',
                        'product',
                        'type',
                        'description',
                        'support_email',
                    ]
                ]
            ]
        ]);
    }
}
