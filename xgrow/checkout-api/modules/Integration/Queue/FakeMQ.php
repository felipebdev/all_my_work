<?php

namespace Modules\Integration\Queue;

use Illuminate\Support\Facades\Log;
use Modules\Integration\Contracts\IQueue;

/**
 * Fake Message Queue
 */
class FakeMQ implements IQueue
{
    public function publish(string $queue, QueueData $data): string
    {
        $json = json_encode($data);

        Log::debug('checkout:fakemq', [
            'json_message' => $json
        ]);

        return $json;
    }
}
