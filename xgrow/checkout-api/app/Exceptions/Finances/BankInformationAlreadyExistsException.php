<?php

namespace App\Exceptions\Finances;

use App\Http\Traits\DontReportInterface;
use Exception;

class BankInformationAlreadyExistsException extends Exception implements DontReportInterface
{
    //
}
