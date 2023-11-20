<?php

namespace Modules\Integration\Events;

class RefreshData extends EventData
{
    public function __construct(object $payload)
    {
        parent::__construct($payload);
    }
}
