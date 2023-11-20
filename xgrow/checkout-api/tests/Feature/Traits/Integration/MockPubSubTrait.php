<?php

namespace Tests\Feature\Traits\Integration;

use Mockery\MockInterface;
use Modules\Messaging\Contracts\PubSubInterface;
use Modules\Messaging\Objects\PublishResponse;

/**
 * This trait mocks the PubSubInterface for testing
 */
trait MockPubSubTrait
{
    /**
     * @return \Mockery\MockInterface
     */
    public function mockPubSub(): MockInterface
    {
        return $this->mock(PubSubInterface::class, function (MockInterface $mock) {
            return $mock->shouldReceive('publishMessage')
                ->andReturn(PublishResponse::ok());
        });
    }

    /**
     * Mocks PubSubInterface and set expected number of calls to method
     *
     * @param  int  $times  Expected number of calls
     * @return \Mockery\MockInterface
     */
    public function mockPubSubWithCount(int $times = 1): MockInterface
    {
        $mock = $this->mockPubSub();
        $mock->expects($this->exactly($times));

        return $mock;
    }
}
