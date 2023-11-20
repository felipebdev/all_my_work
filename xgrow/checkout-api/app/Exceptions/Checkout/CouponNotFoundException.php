<?php

namespace App\Exceptions\Checkout;

use App\Http\Traits\DontReportInterface;
use Exception;

class CouponNotFoundException extends Exception implements DontReportInterface
{
    //
}
