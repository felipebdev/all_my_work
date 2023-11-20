<?php

namespace App\Helpers;

use stdClass;

class JwtWebHelper
{
    private ?stdClass $payload = null;

    public function setPayload(?stdClass $payload): self
    {
        $this->payload = $payload;
        return $this;
    }

    public function getPayload(): stdClass
    {
        return $this->payload ?? new stdClass();
    }

}
