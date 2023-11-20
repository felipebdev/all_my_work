<?php

namespace Modules\Messaging\Exceptions;

use Exception;

/**
 * This exception must be thrown when the Messaging driver is misconfigured, eg: missing env variable
 */
class BadConfigurationMessagingException extends Exception
{
    //
}
