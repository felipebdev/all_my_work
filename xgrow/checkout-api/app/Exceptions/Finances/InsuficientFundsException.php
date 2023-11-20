<?php

namespace App\Exceptions\Finances;

use App\Http\Traits\DontReportInterface;
use Exception;

class InsuficientFundsException extends Exception implements DontReportInterface
{
    //
}
