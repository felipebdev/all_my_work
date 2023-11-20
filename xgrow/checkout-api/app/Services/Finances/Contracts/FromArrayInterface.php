<?php

namespace App\Services\Finances\Contracts;

/**
 * Implement this interface for creating a DTO from an array
 */
interface FromArrayInterface
{
    public static function fromArray(array $array);
}
