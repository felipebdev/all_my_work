<?php

namespace App\Services\Finances\Refund\Objects;

final class UserInfo
{

    public string $platformId;
    public string $userId;

    public function __construct(string $platformId, string $userId)
    {
        $this->platformId = $platformId;
        $this->userId = $userId;
    }

}
