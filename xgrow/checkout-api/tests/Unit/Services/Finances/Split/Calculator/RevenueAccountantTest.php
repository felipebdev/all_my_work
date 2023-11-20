<?php

namespace Tests\Unit\Services\Finances\Split\Calculator;

use App\Exceptions\InvalidPercentageAllocation;
use App\Services\Finances\Split\Calculator\RevenueAccountant;
use PHPUnit\Framework\TestCase;

class RevenueAccountantTest extends TestCase
{

    public function testBasicShare()
    {
        $accountant = new RevenueAccountant();
        $accountant->add('1', 10);
        $accountant->add('2', 20);

        $result = $accountant->share(100);

        $this->assertEquals(10, $result->getAllocationById('1'));
        $this->assertEquals(20, $result->getAllocationById('2'));
        $this->assertEquals(70, $result->getRemainder());
    }

    public function testCentsShare()
    {
        $accountant = new RevenueAccountant();
        $accountant->add('1', 11);

        $result = $accountant->share(10);

        $this->assertEquals(1, $result->getAllocationById('1'));
        $this->assertEquals(9, $result->getRemainder());
    }

    public function testSmallPercentageShare()
    {
        $accountant = new RevenueAccountant();
        $accountant->add('1', 0.5);

        $result = $accountant->share(100);

        $this->assertEquals(0, $result->getAllocationById('1'));
        $this->assertEquals(100, $result->getRemainder());
    }

    public function testNoStakeholder()
    {
        $accountant = new RevenueAccountant();
        $result = $accountant->share(100);
        $this->assertEquals(100, $result->getRemainder());
    }


    public function testRemainderWithFullAllocation()
    {
        $accountant = new RevenueAccountant();
        $accountant->add('1', 25.5);
        $accountant->add('2', 25.5);
        $accountant->add('3', 49);
        $result = $accountant->share(100);
        $this->assertEquals(1, $result->getRemainder()); // remainder of 1 due to half-down rounding
    }

    public function test100PercentToFirstStakeholder()
    {
        $accountant = new RevenueAccountant();
        $accountant->add('1', 100);

        $result = $accountant->share(100);

        $this->assertEquals(100, $result->getAllocationById('1'));
        $this->assertEquals(0, $result->getRemainder());
    }

    public function testExceptionPercentageOver100()
    {
        $this->expectException(InvalidPercentageAllocation::class);

        $accountant = new RevenueAccountant();
        $accountant->add('1', 120);
    }

    public function testExceptionPercentageSumOver100()
    {
        $this->expectException(InvalidPercentageAllocation::class);

        $accountant = new RevenueAccountant();
        $accountant->add('1', 99);
        $accountant->add('2', 2);
    }

    public function testExceptionNegativePercentage()
    {
        $this->expectException(InvalidPercentageAllocation::class);

        $accountant = new RevenueAccountant();
        $accountant->add('1', -10);
    }

}
