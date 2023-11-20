<?php

namespace Tests\Feature\Modules\Messaging;

use Illuminate\Testing\AssertableJsonString;
use Modules\Integration\Services\ExpoPublisher;
use Modules\Integration\Services\Objects\ExpoMessage;
use Ramsey\Uuid\Uuid;
use Tests\Feature\Traits\Integration\MockProducerQueueTrait;
use Tests\TestCase;

use function app;
use function collect;

class ExpoPublisherTest extends TestCase
{
    use MockProducerQueueTrait;

    private ExpoPublisher $expoPublisher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockProducerQueue();

        $this->expoPublisher = app()->make(ExpoPublisher::class);
    }

    public function test_expo_publisher_with_fake_mq()
    {
        $title = 'This is the title';
        $body = 'This is the body';

        $message = new ExpoMessage($title, $body);

        $tokens = collect(['token1', 'token2']);

        $queuedMessage = $this->expoPublisher->pushNotification(Uuid::NIL, $message, $tokens);

        $this->assertIsString($queuedMessage);

        (new AssertableJsonString($queuedMessage))->assertStructure([
            'header' => [
                'date',
                'app' => [
                    'platform_id',
                    'event',
                    'action',
                    'integration' => [
                        'type',
                        'metadata' => [
                            'expoTokens',
                            'messageTitle',
                            'messageBody',
                            'messageData',
                        ],
                    ],
                ],
            ],
            'payload' => [
                'data' => [],
            ],
        ]);
    }
}
