<?php

namespace App\Exceptions;

use Exception;

/**
 * This exception must be thrown when creating a new recipient fails
 */
class RecipientFailedException extends Exception
{
    private array $failures = [];

    public function withFailures(array $failures)
    {
        $this->failures = $failures;
    }

    public function getFailures(): array
    {
        return $this->failures;
    }
}
