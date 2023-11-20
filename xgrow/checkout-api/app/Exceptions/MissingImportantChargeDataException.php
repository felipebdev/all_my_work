<?php

namespace App\Exceptions;

use Exception;

/**
 * This exception must be thrown when an important data is missing and charge is unable to follow.
 *
 * This exception MUST NOT occur on production, but is acceptable on dev/release environments due
 * to database sanitization/cleanup.
 */
class MissingImportantChargeDataException extends Exception
{
    //
}
