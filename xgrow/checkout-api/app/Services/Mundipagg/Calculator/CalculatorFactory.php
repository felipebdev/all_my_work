<?php

namespace App\Services\Mundipagg\Calculator;

use App\Payment;
use App\Services\Mundipagg\Calculator\Contracts\CalculatorStrategy;
use App\Services\Mundipagg\Calculator\Strategies\SimpleCalculator;
use App\Services\Mundipagg\Calculator\Strategies\UnlimitedCalculator;

class CalculatorFactory
{

    public static function getCalculator(string $type): CalculatorStrategy
    {
        switch ($type) {
            case Payment::TYPE_UNLIMITED:
                return resolve(UnlimitedCalculator::class);
            default:
                return resolve(SimpleCalculator::class);
        }
    }
}
