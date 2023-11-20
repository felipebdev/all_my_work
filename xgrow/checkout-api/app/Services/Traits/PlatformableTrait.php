<?php

namespace App\Services\Traits;

trait PlatformableTrait
{

    protected string $platformId;

    public function __construct(?string $platformId = null)
    {
        if ($platformId) {
            $this->setPlatformId($platformId);
        }
    }

    public function setPlatformId(string $platformId): self
    {
        $this->platformId = $platformId;
        return $this;
    }

    public function getPlatformId(): ?string
    {
        return $this->platformId;
    }
}
