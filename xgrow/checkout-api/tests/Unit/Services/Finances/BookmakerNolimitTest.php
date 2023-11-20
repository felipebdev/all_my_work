<?php

namespace Tests\Unit\Services\Finances;

use App\Payment;
use App\Services\Finances\Bookkeeper;
use App\Services\Finances\Bookmaker;
use App\Services\Mundipagg\Calculator\CalculatorFactory;
use App\Services\Mundipagg\Calculator\Contracts\CalculatorStrategy;
use App\Services\Mundipagg\Calculator\Objects\DistributionResult;
use App\Services\Mundipagg\Calculator\Objects\OrderValues;
use PHPUnit\Framework\TestCase;

class BookmakerNolimitTest extends TestCase
{
    private CalculatorStrategy $calculator;

    protected function setUp(): void
    {
        parent::setUp();

        // setup a calculator
        $this->calculator = CalculatorFactory::getCalculator(Payment::TYPE_UNLIMITED);
    }

    public function test_oneProduct_twoInstallments_withInterests()
    {
        $mainResult = new OrderValues(60_00, 0, 95, 1_50);

        $bookkeeper = new Bookkeeper();
        $bookkeeper->setMainValue('mainProduct', $mainResult);

        $bookmaker = new Bookmaker($bookkeeper, $this->calculator);
        $bookmaker->addCharge('master', 65_00); // in "centavos"
        $bookmaker->setNoLimitInstallment(1, 2); // first of 2 installments
        $bookmaker->distribute();

        $expected = new DistributionResult(30_00, 30_00, 65_00, 27_00, 3_00, 0);
        $this->assertEquals($expected, $bookmaker->getDistribution('mainProduct', 'master'));
    }

    public function test_twoProduct_twoInstallments_withInterests()
    {
        $firstResult = new OrderValues(100_00, 0, 95, 1_50);
        $secondResult = new OrderValues(20_00, 0, 95, 0);

        $bookkeeper = new Bookkeeper();
        $bookkeeper->setMainValue(1, $firstResult);
        $bookkeeper->addValue(2, $secondResult);

        $bookmaker = new Bookmaker($bookkeeper, $this->calculator);
        $bookmaker->addCharge('visa', 120_00); // in "centavos"
        $bookmaker->setNoLimitInstallment(1, 2); // first of 2 installments
        $bookmaker->distribute();

        $expectedProduct = new DistributionResult(50_00, 50_00, 100_00, 46_00, 4_00, 0);
        $expectedOrderBump = new DistributionResult(10_00, 10_00, 20_00, 9_50, 50, 0);

        $this->assertEquals($expectedProduct, $bookmaker->getDistribution(1, 'visa'));
        $this->assertEquals($expectedOrderBump, $bookmaker->getDistribution(2, 'visa'));
    }


}
