<?php

namespace Modules\Messaging\Facade;

use Illuminate\Support\Facades\Facade;
use Modules\Messaging\Helpers\PubSubHelper;
use Modules\Messaging\Objects\PublishResponse;

/**
 * @method static PublishResponse publish(string $topic, string $message, array $attributes = [])
 *
 * @see \Modules\Messaging\Helpers\PubSubHelper
 */
class PubSub extends Facade
{
    protected static function getFacadeAccessor()
    {
        return PubSubHelper::class;
    }
}
