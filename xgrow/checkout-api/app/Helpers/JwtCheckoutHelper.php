<?php

namespace App\Helpers;

class JwtCheckoutHelper
{
    private $token;
    private $plan;
    private $platformId;

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;
        return $this;
    }

    public function getPlanId(): ?int
    {
        return $this->plan;
    }

    public function setPlanId(int $plan): self
    {
        $this->plan = $plan;
        return $this;
    }

    public function getPlatformId(): ?string
    {
        return $this->platformId;
    }

    public function setPlatformId(string $platformId)
    {
        $this->platformId = $platformId;
        return $this;
    }

}
