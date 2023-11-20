<?php

namespace Modules\Integration\Queue;

use JsonSerializable;
use Modules\Integration\Contracts\IEventData;

class QueuePayload implements JsonSerializable
{
    /**
     * @var object
     */
    private $data;

    public function __construct(IEventData $data)
    {
        $this->data = $data->getAttributes();
    }

    /**
     * @return object
     */ 
    public function getData()
    {
        return $this->data;
    }

    public function jsonSerialize()
    {
        return [
            'data' => $this->data
        ];
    }
}
