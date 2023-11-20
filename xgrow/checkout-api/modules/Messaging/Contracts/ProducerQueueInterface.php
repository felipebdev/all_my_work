<?php

namespace Modules\Messaging\Contracts;

use Modules\Messaging\Objects\ProduceResponse;

/**
 * This is the basic interface for a Producer in a Queue (Producer/Consumer model)
 */
interface ProducerQueueInterface
{
    /**
     * Send a message to queue in given queueName
     *
     * @param  string  $queueName  Name of the queue
     * @param  string  $message  Message do be sent
     * @param  array  $attributes  Optional attributes (may be ignored by Queue driver)
     * @return \Modules\Messaging\Objects\ProduceResponse
     */
    public function queue(string $queueName, string $message, array $attributes = []): ProduceResponse;
}
