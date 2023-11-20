<?php

namespace Tests\Services\Mundipagg\Calculator\Strategies;

use App\Payment;
use App\Services\Mundipagg\Calculator\Strategies\CalculatorLegacy;
use PHPUnit\Framework\TestCase;

/**
 * Legacy calculator doesn't need tests anymore
 */
class CalculatorLegacyTestCase extends TestCase
{

    /**
     * This test covers an specific example case where rounding is NOT executed properly
     */
    public function testCornerCase()
    {
        $expected = CalculatorLegacy::v1(
            $mainPlanPrice = 100,
            $mainPlanValue = 100,
            $mainCoupon = 10,
            $paymentType = Payment::TYPE_SALE,
            $installments = 1,
            $percentSplit = 90.65,
            $clientTaxTransaction = 1.5
        );

        $mainPrice = round($expected[0], 2);
        $mainCustomerValue = round($expected[2], 2);
        $mainTax = round($expected[3], 2);

        $sum = $mainCustomerValue + $mainTax;

        //Failed asserting that 90.01 matches expected 90.0.
        //Expected :90
        //Actual   :90.01

        // assert bad totalization
        $this->assertNotEquals($mainPrice, $sum);

        // expected value (wrong value)
        $this->assertEquals(90.01, $sum);
    }
}
