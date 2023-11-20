<?php

namespace Modules\Messaging\Drivers\BullMQ;

use Illuminate\Support\Facades\Redis;
use Modules\Messaging\Contracts\ProducerQueueInterface;
use Modules\Messaging\Objects\ProduceResponse;

/**
 * Raw BullMQ producer driver implementation using direct write to Redis
 */
class BullMQProducer implements ProducerQueueInterface
{
    const MAX_ATTEMPTS = 3;
    const FIXED_DELAY = 3000;

    public function queue(string $queueName, string $message, array $attributes = []): ProduceResponse
    {
        $uuid = preg_replace('/(0)\.(\d+) (\d+)/', '$3$1$2', microtime());
        $opts = [
            'attempts' => self::MAX_ATTEMPTS,
            'backoff' => [
                'type' => "fixed",
                'delay' => self::FIXED_DELAY,
            ]
        ];

        Redis::command('hmset', ["bull:{$queueName}:$uuid", 'data', $message]);
        Redis::command('hmset', ["bull:{$queueName}:$uuid", 'opts', json_encode($opts)]);
        Redis::command('zadd', ["bull:{$queueName}:delayed", 1, $uuid]);

        return ProduceResponse::ok($message);
    }
}
