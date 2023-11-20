<?php

namespace Modules\Messaging\Contracts;

use Modules\Messaging\Objects\PublishResponse;

/**
 * This is the basic Publisher/Subscriber interface
 */
interface PubSubInterface
{
    public function publishMessage(string $topic, string $message, array $attributes = []): PublishResponse;
}
