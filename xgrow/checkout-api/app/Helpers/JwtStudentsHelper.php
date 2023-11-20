<?php

namespace App\Helpers;

use stdClass;

class JwtStudentsHelper
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

    public function getEmail(): ?string
    {
        return $this->payload->email ?? null;
    }

    public function getSubscribersIds(): array
    {
        return $this->payload->subscribers_ids ?? [];
    }

    public function getProductsIds(): array
    {
        return $this->payload->products_id ?? [];
    }

}
