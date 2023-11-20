<?php

namespace Modules\Messaging\Helpers;

use Modules\Messaging\Contracts\PubSubInterface;
use Modules\Messaging\Objects\PublishResponse;

class PubSubHelper
{
    private PubSubInterface $genericPubSub;

    public function __construct(PubSubInterface $genericPubSub)
    {
        $this->genericPubSub = $genericPubSub;
    }

    public function publish(string $topic, string $message, array $attributes = []): PublishResponse
    {
        return $this->genericPubSub->publishMessage($topic, $message, $attributes);
    }

}
