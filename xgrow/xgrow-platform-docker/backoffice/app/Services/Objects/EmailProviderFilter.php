<?php

namespace App\Services\Objects;

class EmailProviderFilter
{
    public ?string $search = null;

    public function __construct(
        ?string $search = null
    ) {
        $this->search = $search;
    }

    /**
     * @param  string|null  $search
     * @return PlanFilter
     */
    public function setSearch(?string $search): EmailProviderFilter
    {
        $this->search = $search;
        return $this;
    }


}
