<?php

namespace Modules\Integration\Queue;

use JsonSerializable;
use Modules\Integration\Models\Action;

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

    public function __construct(Action $action)
    {
        $this->action = $action;
        $this->date = date('Y-m-d H:i:s');
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
        ];
    }
}
