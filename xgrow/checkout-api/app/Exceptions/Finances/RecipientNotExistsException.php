<?php

namespace App\Exceptions\Finances;

use Exception;
use Throwable;

/**
 * This exception is thrown when requested action can not be performed due to given recipient ID not exists on Gateway.
 *
 * Eg: recipient_id found on DB, but not exists on Pagar.me
 */
class RecipientNotExistsException extends Exception
{
    public const DEFAULT_MESSAGE = 'Given recipient ID not exists';

    public function __construct($message = self::DEFAULT_MESSAGE, $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
