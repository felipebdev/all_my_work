<?php

namespace App\Exceptions\Finances;

use App\Http\Traits\DontReportInterface;
use Exception;

/**
 * This exception is thrown when requested action can not be performed due to already existing recipient.
 */
class RecipientAlreadyExistsException extends Exception implements DontReportInterface
{
    //
}
