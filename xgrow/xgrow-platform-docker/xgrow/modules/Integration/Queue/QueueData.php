<?php

namespace Modules\Integration\Queue;

use JsonSerializable;

class QueueData implements JsonSerializable
{
    /**
     * @var QueueHeader
     */
    private $header;

    /**
     * @var QueuePayload
     */
    private $payload;

    public function __construct(
        QueueHeader $header,
        QueuePayload $payload
    ) {
        $this->header = $header;
        $this->payload = $payload;
    }
    
    /**
     * @return Modules\Integration\Queue\QueueHeader
     */ 
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @return Modules\Integration\Queue\QueuePayload
     */ 
    public function getPayload()
    {
        return $this->payload;
    }

    public function jsonSerialize()
    {
        return [
            'header' => $this->header,
            'payload' => $this->payload
        ];
    }
}
