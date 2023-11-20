<?php

namespace App\Services\Objects;

class EmailFilter extends BaseFilter
{
    public ?string $search = null;

    public function __construct(
        ?string $search = null
    ) {
        $this->search = $search;
    }

    /**
     * @param  string|null  $search
     * @return EmailFilter
     */
    public function setSearch(?string $search): EmailFilter
    {
        $this->search = $search;
        return $this;
    }

}
