<?php

namespace Modules\Integration\Contracts;

use Modules\Integration\Queue\QueueData;

interface IQueue
{
    /**
     * @param  string  $queue
     * @param  \Modules\Integration\Queue\QueueData  $data
     * @return string
     */
    public function publish(string $queue, QueueData $data): string;
}
