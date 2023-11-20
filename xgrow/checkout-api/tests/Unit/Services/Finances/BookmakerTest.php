<?php

namespace Tests\Unit\Services\Finances;

use App\Exceptions\ValueMismatchException;
use App\Payment;
use App\Services\Finances\Bookkeeper;
use App\Services\Finances\Bookmaker;
use App\Services\Mundipagg\Calculator\CalculatorFactory;
use App\Services\Mundipagg\Calculator\Contracts\CalculatorStrategy;
use App\Services\Mundipagg\Calculator\Objects\DistributionResult;
use App\Services\Mundipagg\Calculator\Objects\OrderValues;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * Class BookmakerTest
 *
 * @note Uses "Numeric Literal Separator" from PHP 7.4 for better code readability of money values (1234 === 12_34)
 *
 * @package Tests\Unit\Services\Finances
 */
class BookmakerTest extends TestCase
{
    private CalculatorStrategy $calculator;

    protected function setUp(): void
    {
        parent::setUp();

        // setup a calculator
        $this->calculator = CalculatorFactory::getCalculator(Payment::TYPE_SALE);
    }

    /*
     * One product, single payment
     */

    public function testOneProductSinglePayment_noInterests()
    {
        $bookkeeper = new Bookkeeper();
        $firstValue = new OrderValues(100_00, 0, 95, 1_50);
        $bookkeeper->setMainValue(1, $firstValue);

        $bookmaker = new Bookmaker($bookkeeper, $this->calculator);
        $bookmaker->addCharge('visa', 100_00); // in "centavos"

        $result = $bookmaker->distribute();

        $expected = new DistributionResult(100_00, 100_00, 100_00, 93_50, 6_50, 0);

        $this->assertEquals($expected, $result->getDistribution(1, 'visa'));
    }

    public function testOneProductSinglePayment_noInterests_withCoupon()
    {
        $bookkeeper = new Bookkeeper();
        $firstValue = new OrderValues(100_00, 10_00, 95, 1_50);
        $bookkeeper->setMainValue(1, $firstValue);

        $bookmaker = new Bookmaker($bookkeeper, $this->calculator);
        $bookmaker->addCharge('visa', 90_00); // in "centavos"

        $result = $bookmaker->distribute();

        $expected = new DistributionResult(100_00, 90_00, 90_00, 84_00, 6_00, 10_00);

        $this->assertEquals($expected, $result->getDistribution(1, 'visa'));
    }

    public function testOneProductSinglePayment_withInterests()
    {
        $bookkeeper = new Bookkeeper();
        $mainResult = new OrderValues(60_00, 0, 95, 1_50);

        $bookmaker = new Bookmaker($bookkeeper, $this->calculator);
        $bookmaker->addCharge('master', 65_36); // in "centavos"
        $bookkeeper->setMainValue('mainProduct', $mainResult); //

        $bookmaker->distribute();

        $expected = new DistributionResult(60_00, 60_00, 65_36, 55_50, 4_50, 0);
        $this->assertEquals($expected, $bookmaker->getDistribution('mainProduct', 'master'));
    }

    /*
     * One product, multiple means
     */

    public function testOneProductTwoPayments_noInterests()
    {
        $bookkeeper = new Bookkeeper();
        $mainResult = new OrderValues(90_00, 0, 95, 1_50);
        $bookkeeper->setMainValue('mainProduct', $mainResult); //

        $bookmaker = new Bookmaker($bookkeeper, $this->calculator);
        $bookmaker->addCharge('visa', 60_00); // in "centavos"
        $bookmaker->addCharge('master', 30_00); // in "centavos"
        $bookmaker->distribute();

        $expectedVisa = new DistributionResult(60_00, 60_00, 60_00, 56_00, 4_00, 0);
        $expectedMaster = new DistributionResult(30_00, 30_00, 30_00, 28_00, 2_00, 0);

        $this->assertEquals($expectedVisa, $bookmaker->getDistribution('mainProduct', 'visa'));
        $this->assertEquals($expectedMaster, $bookmaker->getDistribution('mainProduct', 'master'));
    }

    public function testOneProductTwoPayments_withInterestsOnSecond()
    {
        $bookkeeper = new Bookkeeper();
        $firstValue = new OrderValues(100_00, 0, 95, 1_50);
        $bookkeeper->setMainValue(1, $firstValue);

        $bookmaker = new Bookmaker($bookkeeper, $this->calculator);
        $bookmaker->addCharge('visa', 50_00); // in "centavos"
        $bookmaker->addCharge('master', 100_00); // in "centavos"

        $bookmaker->distribute();

        $expected1 = new DistributionResult(33_34, 33_34, 50_00, 31_17, 2_17, 0);
        $expected2 = new DistributionResult(66_66, 66_66, 100_00, 62_33, 4_33, 0);

        $this->assertEquals($expected1, $bookmaker->getDistribution(1, 'visa'));
        $this->assertEquals($expected2, $bookmaker->getDistribution(1, 'master'));
    }

    public function testOneProductTwoPayments_withInterestsOnBoth()
    {
        $bookkeeper = new Bookkeeper();
        $mainResult = new OrderValues(100_00, 0, 95, 1_50);
        $bookkeeper->setMainValue('mainProduct', $mainResult); //

        $bookmaker = new Bookmaker($bookkeeper, $this->calculator);
        $bookmaker->addCharge('visa', 31_32); // in "centavos"
        $bookmaker->addCharge('master', 76_26); // in "centavos"
        $bookmaker->distribute();

        $expectedVisa = new DistributionResult(29_12, 29_12, 31_32, 27_23, 1_89, 0);
        $expectedMaster = new DistributionResult(70_88, 70_88, 76_26, 66_27, 4_61, 0);

        $this->assertEquals($expectedVisa, $bookmaker->getDistribution('mainProduct', 'visa'));
        $this->assertEquals($expectedMaster, $bookmaker->getDistribution('mainProduct', 'master'));
    }

    /*
     * Product + Order Bump, Single payment
     */

    public function testTwoProductsSinglePayment_noInterests()
    {
        $bookkeeper = new Bookkeeper();

        $firstResult = new OrderValues(100_00, 0, 95, 1_50);
        $secondResult = new OrderValues(20_00, 0, 95, 0);

        $bookkeeper->setMainValue(1, $firstResult);
        $bookkeeper->addValue(2, $secondResult);

        $bookmaker = new Bookmaker($bookkeeper, $this->calculator);

        $bookmaker->addCharge('visa', 120_00); // in "centavos"
        $bookmaker->distribute();

        $expectedProduct = new DistributionResult(100_00, 100_00, 100_00, 93_50, 6_50, 0);
        $expectedOrderBump = new DistributionResult(20_00, 20_00, 20_00, 19_00, 1_00, 0);

        $this->assertEquals($expectedProduct, $bookmaker->getDistribution(1, 'visa'));
        $this->assertEquals($expectedOrderBump, $bookmaker->getDistribution(2, 'visa'));
    }

    /*
     * Product + Order Bump, multiple means
     */

    public function testTwoProductsTwoPayments_noInterests()
    {
        $bookkeeper = new Bookkeeper();

        $firstResult = new OrderValues(100_00, 0, 95, 1_50);
        $secondResult = new OrderValues(20_00, 0, 95, 0);

        $bookkeeper->setMainValue(1, $firstResult);
        $bookkeeper->addValue(2, $secondResult);

        $bookmaker = new Bookmaker($bookkeeper, $this->calculator);

        $bookmaker->addCharge('visa', 80_00); // in "centavos"
        $bookmaker->addCharge('master', 40_00); // in "centavos"

        $bookmaker->distribute();

        $expectedProductVisa = new DistributionResult(66_68, 66_68, 66_68, 62_35, 4_33, 0);
        $expectedProductMaster = new DistributionResult(33_33, 33_33, 33_33, 31_16, 2_17, 0);

        $expectedOrderBumpVisa = new DistributionResult(13_33, 13_33, 13_33, 12_66, 67, 0);
        $expectedOrderBumpMaster = new DistributionResult(6_66, 6_66, 6_66, 6_33, 33, 0);

        $this->assertEquals($expectedProductVisa, $bookmaker->getDistribution(1, 'visa'));
        $this->assertEquals($expectedProductMaster, $bookmaker->getDistribution(1, 'master'));
        $this->assertEquals($expectedOrderBumpVisa, $bookmaker->getDistribution(2, 'visa'));
        $this->assertEquals($expectedOrderBumpMaster, $bookmaker->getDistribution(2, 'master'));
    }

    // two products, two payments (both with interests) and coupon
    public function testTwoProductsTwoPayments_bothPaymentsWithInterests()
    {
        $bookkeeper = new Bookkeeper();

        $firstResult = new OrderValues(110_00, 10_00, 95, 1_50);
        $secondResult = new OrderValues(20_00, 0, 95, 0);

        $bookkeeper->setMainValue(1, $firstResult);
        $bookkeeper->addValue(2, $secondResult);

        $bookmaker = new Bookmaker($bookkeeper, $this->calculator);

        $bookmaker->addCharge('visa', 100_00); // in "centavos"
        $bookmaker->addCharge('master', 60_00); // in "centavos"

        $bookmaker->distribute();

        $expectedProductVisa = new DistributionResult(68_75, 62_50, 83_34, 58_44, 4_06, 6_25);
        $expectedProductMaster = new DistributionResult(41_25, 37_50, 50_00, 35_06, 2_44, 3_75);

        $expectedOrderBumpVisa = new DistributionResult(12_50, 12_50, 16_66, 11_87, 63, 0);
        $expectedOrderBumpMaster = new DistributionResult(7_50, 7_50, 10_00, 7_13, 37, 0);

        $this->assertEquals($expectedProductVisa, $bookmaker->getDistribution(1, 'visa'));
        $this->assertEquals($expectedProductMaster, $bookmaker->getDistribution(1, 'master'));
        $this->assertEquals($expectedOrderBumpVisa, $bookmaker->getDistribution(2, 'visa'));
        $this->assertEquals($expectedOrderBumpMaster, $bookmaker->getDistribution(2, 'master'));
    }

    /*
     * Error cases
     */

    public function testExceptionWrongIndex()
    {
        $bookkeeper = new Bookkeeper();
        $mainResult = new OrderValues(100_00, 10_00, 95, 1_50);
        $bookkeeper->setMainValue('banana', $mainResult); //

        $bookmaker = new Bookmaker($bookkeeper, $this->calculator);
        $bookmaker->addCharge('visa', 100_00); // in "centavos"
        $bookmaker->distribute();

        $this->expectException(RuntimeException::class);
        $bookmaker->getDistribution('kiwi', 'visa')->getDecimalCustomerValue();
    }

    public function testExceptionInvalidTotal()
    {
        $bookkeeper = new Bookkeeper();
        $mainResult = new OrderValues(100_00, 10, 85, 1_50);
        $bookkeeper->setMainValue('banana', $mainResult); //

        $bookmaker = new Bookmaker($bookkeeper, $this->calculator);
        $bookmaker->addCharge('visa', 80_00); // in "centavos"

        $this->expectException(ValueMismatchException::class);
        $bookmaker->distribute();
    }

}
