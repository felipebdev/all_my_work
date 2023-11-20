<?php

namespace Modules\Integration\Contracts;

use Modules\Integration\Queue\QueueData;

interface IQueue
{
    /**
     * @param string $queue
     * @param QueueData $data
     * @return void
     */
    public function publish(
        string $queue,
        QueueData $data
    ): void;
}
