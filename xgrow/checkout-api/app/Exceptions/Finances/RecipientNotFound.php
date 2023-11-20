<?php

namespace App\Exceptions\Finances;

use App\Http\Traits\DontReportInterface;
use Exception;
use Throwable;

/**
 * This exception is thrown when requested action can not be performed due lack of recipient ID.
 *
 * Eg: client.recipient_id = null
 */
class RecipientNotFound extends Exception implements DontReportInterface
{
    public const DEFAULT_MESSAGE = 'Recipient not found for this platform';

    public function __construct($message = self::DEFAULT_MESSAGE, $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
