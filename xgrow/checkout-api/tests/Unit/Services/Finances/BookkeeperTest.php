<?php

namespace Tests\Unit\Services\Finances;

use App\Services\Finances\Bookkeeper;
use App\Services\Mundipagg\Calculator\Objects\OrderValues;
use PHPUnit\Framework\TestCase;

class BookkeeperTest extends TestCase
{


    public function testGetMainId()
    {
        $bookkeeper = new Bookkeeper();
        $firstResult = new OrderValues(10000, 1000, 95, 150);
        $bookkeeper->setMainValue(1, $firstResult);
        $secondResult = new OrderValues(2000, 0, 95, 0);
        $bookkeeper->addValue(2, $secondResult);

        $this->assertEquals(1, $bookkeeper->getMainId());
    }

    public function testGetMainValue()
    {
        $bookkeeper = new Bookkeeper();
        $firstResult = new OrderValues(10000, 1000, 95, 150);
        $bookkeeper->setMainValue(1, $firstResult);
        $secondResult = new OrderValues(2000, 0, 95, 0);
        $bookkeeper->addValue(2, $secondResult);

        $this->assertEquals($firstResult, $bookkeeper->getMainValue());
    }

    public function testGetMainValueById()
    {
        $bookkeeper = new Bookkeeper();
        $firstResult = new OrderValues(10000, 1000, 95, 150);
        $bookkeeper->setMainValue(1, $firstResult);
        $secondResult = new OrderValues(2000, 0, 95, 0);
        $bookkeeper->addValue(2, $secondResult);

        $this->assertEquals($firstResult, $bookkeeper->getValueById(1));
    }

    public function testGetValueById()
    {
        $bookkeeper = new Bookkeeper();
        $firstResult = new OrderValues(10000, 1000, 95, 150);
        $bookkeeper->setMainValue(1, $firstResult);
        $secondResult = new OrderValues(2000, 0, 95, 0);
        $bookkeeper->addValue(2, $secondResult);

        $this->assertEquals($secondResult, $bookkeeper->getValueById(2));
    }

    public function testMainOverwrite()
    {
        $bookkeeper = new Bookkeeper();
        $firstResult = new OrderValues(10000, 1000, 95, 150);
        $bookkeeper->setMainValue(1, $firstResult);
        $secondResult = new OrderValues(2000, 0, 95, 0);
        $bookkeeper->addValue(2, $secondResult);

        $newResult = new OrderValues(15000, 1000, 95, 150);
        $bookkeeper->addValue(1, $newResult);

        $this->assertEquals($newResult, $bookkeeper->getMainValue());
    }

}
