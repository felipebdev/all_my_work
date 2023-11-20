<?php

namespace Tests\Feature\Modules\Integration;

use App\Transaction;
use App\Utils\TriggerIntegrationJob;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\AssertableJsonString;
use Modules\Integration\Contracts\IQueue;
use Modules\Integration\Enums\EventEnum;
use Modules\Integration\Events\TransactionData;
use Modules\Integration\Queue\FakeMQ;
use Modules\Integration\Services\IntegrationPublisher;
use Tests\Feature\Helper\MundipaggToken;
use Tests\Feature\Traits\CreateSubscriberTrait;
use Tests\Feature\Traits\LocalDatabaseIds;
use Tests\TestCase;

class IntegrationPublisherRefusedPaymentTest extends TestCase
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

    public function test_assert_payment_refused_payload()
    {
        $this->withoutMiddleware();

        // create a failed transaction
        $subscriberId = $this->createSubscriber($this->platformId, $this->salePlanId);

        $token = MundipaggToken::randomInvalid($this->faker->creditCardNumber('American Express'));

        $response = $this->postJson("/api/checkout/{$this->platformId}/{$this->salePlanId}", [
            "payment_method" => "credit_card",
            "subscriber_id" => $subscriberId,
            "platform_id" => $this->platformId,
            "plan_id" => $this->salePlanId, // 100
            "order_bump" => [],
            "cc_info" => [
                [
                    "token" => "$token",
                    "installment" => 1,
                    "value" => "100.00"
                ]
            ],
        ]);

        $response->assertStatus(400);

        /// publish an integration

        $transactionData = new TransactionData(Transaction::inRandomOrder()->first());

        $result = $this->integrationPublisher->publishIntegrations(
            EventEnum::PAYMENT_REFUSED,
            $this->platformId,
            [$this->salePlanId],
            $transactionData
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
                    'subscriber_plan_id',
                    'subscriber_email',
                    'subscriber_name',
                    'subscriber_phone',
                    'subscriber_birthday',
                    'subscriber_zipcode',
                    'subscriber_street',
                    'subscriber_number',
                    'subscriber_comp',
                    'subscriber_district',
                    'subscriber_city',
                    'subscriber_state',
                    'subscriber_country',
                    'subscriber_document_type',
                    'subscriber_document_number',
                    'subscriber_phone_country_code',
                    'subscriber_phone_area_code',
                    'subscriber_phone_number',
                    'transaction_id',
                    'transaction_platform_id',
                    'transaction_order_code',
                    'transaction_status',
                    'transaction_type',
                    'transaction_origin',
                    'transaction_op_code',
                    'transaction_op_message',
                    'transaction_total',
                    'transaction_plans' => [
                        '*' => [
                            'id',
                            'plan',
                            'type',
                            'price',
                        ],
                    ],
                    'payment_order_code',
                    'payment_price',
                    'payment_status',
                    'payment_type',
                    'change_card_url',
                ]
            ]
        ]);
    }
}
