<?php

namespace App\Exceptions\Checkout;

use App\Http\Traits\DontReportInterface;
use Exception;

class CouponUnavailableException extends Exception implements DontReportInterface
{
    //
}
