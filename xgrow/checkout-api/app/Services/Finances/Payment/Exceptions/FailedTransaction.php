<?php

namespace App\Services\Finances\Payment\Exceptions;

use Exception;

/**
 * This exception must be thrown when order request is apparently acceptable but
 * payment gateway failed or refused the order request
 *
 * @package App\Services\Finances\Payment\Exceptions
 */
class FailedTransaction extends Exception
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
