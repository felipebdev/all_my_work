<?php

namespace Modules\Integration\Queue;

use JsonSerializable;
use Modules\Integration\Models\Action;
use Webpatser\Uuid\Uuid;

class QueueHeader implements JsonSerializable
{
    /**
     * @var Modules\Integration\Models\Action
     */
    private $action;

    /**
     * @var string (Y-m-d H:i:s)
     */
    private $date;

    private ?string $correlationId = null;

    public function __construct(Action $action, ?string $correlationId = null)
    {
        $this->action = $action;
        $this->date = date('Y-m-d H:i:s');
        $this->correlationId = $correlationId;
    }

    /**
     * @return array
     */ 
    public function getAction()
    {
        return $this->action;
    }

    public function jsonSerialize()
    {
        $this->action->planIds = $this->action
            ->plans()
            ->distinct()
            ->allRelatedIds();

        return [
            'date' => $this->date,
            'app' => $this->action,
            'correlation_id' => $this->correlationId ?? (string) Uuid::generate(4),
        ];
    }
}
