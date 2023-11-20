<?php

namespace App\Services\Contracts;

interface RejectableInterface
{
    /**
     * Returns the list of rejected values
     *
     * @return array
     */
    public function getRejected(): array;
}
