<?php

namespace App\Helpers;

use App\Subscriber;

class JwtPlatformHelper
{

    private $token;
    private $subscriber;
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

    public function getSubscriber(): ?Subscriber
    {
        return $this->subscriber;
    }

    public function setSubscriber(Subscriber $subscriber): self
    {
        $this->subscriber = $subscriber;
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
