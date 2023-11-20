<?php

namespace Tests\Feature\Traits\Integration;

use Mockery\MockInterface;
use Modules\Messaging\Contracts\ProducerQueueInterface;
use Modules\Messaging\Objects\ProduceResponse;

/**
 * This trait mocks the ProducerQueueInterface for testing
 */
trait MockProducerQueueTrait
{
    /**
     * Call this method to mock ProducerQueue
     *
     * @return \Mockery\MockInterface
     */
    public function mockProducerQueue(): MockInterface
    {
        return $this->mock(ProducerQueueInterface::class, function (MockInterface $mock) {
            return $mock->shouldReceive('queue')
                ->andReturn(ProduceResponse::ok());
        });
    }
}
