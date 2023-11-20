<?php

namespace Modules\Integration\Queue;

use Modules\Integration\Contracts\IQueue;
use Modules\Messaging\Drivers\BullMQ\BullMQProducer;

/**
 * Integration queue using BullMQ
 */
class BullMQ implements IQueue
{

    private BullMQProducer $bullMQProducer;

    public function __construct(BullMQProducer $bullMQProducer)
    {
        $this->bullMQProducer = $bullMQProducer;
    }

    /**
     * @param  string  $queue
     * @param  \Modules\Integration\Queue\QueueData  $data
     * @return string
     */
    public function publish(string $queue, QueueData $data): string
    {
        $json = json_encode($data); // serialize QueueData

        $this->bullMQProducer->queue($queue, $json);

        return $json;
    }
}
