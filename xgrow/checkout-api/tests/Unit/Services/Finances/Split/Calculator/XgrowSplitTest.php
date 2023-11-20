<?php

namespace Tests\Unit\Services\Finances\Split\Calculator;

use App\Services\Finances\Split\Calculator\Objects\XgrowSplitResult;
use App\Services\Finances\Split\Calculator\XgrowSplit;
use PHPUnit\Framework\TestCase;

class XgrowSplitTest extends TestCase
{
    public function testBasic()
    {
        $result = XgrowSplit::calculate(10000, 10000, 150, 95.0);
        $expected = new XgrowSplitResult(9350, 650, 650, 150);
        $this->assertEquals($expected, $result);
    }

    public function testBasicWithRounding()
    {
        $result = XgrowSplit::calculate(10000, 10000, 150, 95.005);
        $expected = new XgrowSplitResult(9350, 650, 650, 150);
        $this->assertEquals($expected, $result);
    }

    public function testWithInterests()
    {
        $result = XgrowSplit::calculate(11000, 10000, 150, 95);
        // $serviceValue and $xgrowAmount receive the interest
        $expected = new XgrowSplitResult(9350, 1650, 650, 150);
        $this->assertEquals($expected, $result);
    }

    public function test100PercentSplitWithInterests()
    {
        $result = XgrowSplit::calculate(11000, 10000, 150, 100);
        $expected = new XgrowSplitResult(9850, 1150, 150, 150);
        $this->assertEquals($expected, $result);
    }

    public function testBasicWithAnticipation()
    {
        $this->markTestSkipped('Anticipation disabled');

        $result = XgrowSplit::calculate(10000, 10000, 150, 95.0);
        $expected = new XgrowSplitResult(9350, 650, 650, 150);
        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider percentProvider
     */
    public function testRoundingErrorOnPercentageVariation($percentage)
    {
        $result = XgrowSplit::calculate(10000, 10000, 150, $percentage);
        $this->assertEquals(10000, (int) $result->getCustomerAmount() + (int) $result->getTaxAmount());
    }

    public function percentProvider()
    {
        foreach (range(10.005, 100, 0.3) as $percent) {
            yield [$percent];
        }
    }
}
