<?php

namespace App\Exceptions;

use Exception;

class InvalidOperationException extends Exception
{
    private array $errors;

    /**
     * InvalidOperationException constructor.
     * @param  array  $errors
     * @param  int  $code
     * @param  \Throwable|null  $previous
     */
    public function __construct(array $errors, $code = 0, \Throwable $previous = null)
    {
        parent::__construct(join("\n", $errors), $code, $previous);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

}
