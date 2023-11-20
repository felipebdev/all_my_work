<?php

namespace Modules\Integration\Queue;

use Illuminate\Support\Facades\Redis;
use Modules\Integration\Contracts\IQueue;

class BullMQ implements IQueue
{
    /**
     * @param string $queue
     * @param QueueData $data
     * @return void
     */
    public function publish(
        string $queue,
        QueueData $data
    ): void {
        $uuid = preg_replace('/(0)\.(\d+) (\d+)/', '$3$1$2', microtime());
        Redis::command('hmset',["bull:{$queue}:$uuid", 'data' , json_encode($data)]);
        Redis::command('zadd', ["bull:{$queue}:delayed", 1, $uuid]);
    }
}
