<?php

namespace Tests\Unit\Services\Mundipagg\Calculator\Strategies;

use App\Services\Mundipagg\Calculator\Objects\AmountResult;
use App\Services\Mundipagg\Calculator\Strategies\SimpleCalculator;
use PHPUnit\Framework\TestCase;

class SimpleCalculatorTest extends TestCase
{

    public function percentProvider()
    {
        foreach (range(0, 100, 31.1) as $percent) {
            foreach (range(1, 9, 3) as $installment) {
                foreach (range(1.5, 4) as $transactionTax) {
                    yield [$percent, $installment, $transactionTax];
                }
            }
        }
    }

    /**
     * @dataProvider percentProvider
     */
    public function testPreciseCalculation($percent, $installments, $transactionTax)
    {
        $price = 213_00;
        $coupon = 10_00;
        $result = (new SimpleCalculator())
            ->calculate($price, $coupon, $percent, $transactionTax, 1, $installments);

        $valueWithDiscounts = round($result->getValueWithDiscounts(), 2);

        $customerValue = round($result->getCustomerValue(), 2);
        $tax = round($result->getTax(), 2);

        // MUST NOT have rounding errors
        $this->assertEquals($valueWithDiscounts, $customerValue + $tax);
    }

    public function testRealCaseScenario()
    {
        $price = 100_00;
        $coupon = 0;
        $installments = 12;
        $percentSplit = 95;
        $clientTaxTransaction = 1_50;

        $result = (new SimpleCalculator())
            ->calculate($price, $coupon, $percentSplit, $clientTaxTransaction, 1, $installments);

        $expected = new AmountResult(100_00, 100_00, 93_50, 6_50, 0);

        $this->assertEquals($expected, $result);
    }

}
