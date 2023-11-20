<?php

namespace App\Services\Objects;

class UserFilter extends BaseFilter
{
    public ?string $search = null;
    public ?bool $status = null;

    public function __construct(
        ?string $search = null,
        ?bool $status = null
    ) {
        $this->search = $search;
        $this->status = $status;
    }

    /**
     * @param  string|null  $search
     * @return UserFilter
     */
    public function setSearch(?string $search): UserFilter
    {
        $this->search = $search;
        return $this;
    }

    /**
     * @param bool|null $status
     * @return UserFilter
     */
    public function setStatus(?bool $status): UserFilter
    {
        $this->status = $status;
        return $this;
    }

}
