<?php

namespace Tests\Unit;

use App\Services\Finances\Objects\Coin;
use PHPUnit\Framework\TestCase;

class CoinTest extends TestCase
{

    public function test_coin_from_int()
    {
        $coin = Coin::fromInt(10_00, 'BRL');

        $this->assertEquals(1000, $coin->getAmount());
        $this->assertEquals('BRL', $coin->getCurrency());
    }

    public function test_coin_from_decimal()
    {
        $coin = Coin::fromDecimal(10.00, 'BRL');

        $this->assertEquals(1000, $coin->getAmount());
        $this->assertEquals('BRL', $coin->getCurrency());
    }

    public function test_coin_from_int_with_currency()
    {
        $coin = Coin::fromInt(10_00, 'USD');

        $this->assertEquals(1000, $coin->getAmount());
        $this->assertEquals('USD', $coin->getCurrency());
    }

    public function test_coin_from_decimal_with_currency()
    {
        $coin = Coin::fromDecimal(10.00, 'USD');

        $this->assertEquals(1000, $coin->getAmount());
        $this->assertEquals('USD', $coin->getCurrency());
    }

    public function test_coin_to_decimal()
    {
        $coin = Coin::fromInt(10_00, 'BRL');

        $this->assertEquals(10.00, $coin->getDecimal());
        $this->assertEquals('BRL', $coin->getCurrency());
    }

    public function test_allocate_100_coins_to_1()
    {
        $coin = Coin::fromInt(100, 'BRL');

        $this->assertEquals([
            Coin::fromInt(100, 'BRL'),
        ], $coin->allocateInstallments(1));
    }

    public function test_allocate_100_coins_to_3()
    {
        $coin = Coin::fromInt(100, 'BRL');

        $this->assertEquals([
            Coin::fromInt(34, 'BRL'),
            Coin::fromInt(33, 'BRL'),
            Coin::fromInt(33, 'BRL'),
        ], $coin->allocateInstallments(3));
    }

    public function test_allocate_100_coins_to_7()
    {
        $coin = Coin::fromInt(100, 'BRL');

        $this->assertEquals([
            Coin::fromInt(16, 'BRL'),
            Coin::fromInt(14, 'BRL'),
            Coin::fromInt(14, 'BRL'),
            Coin::fromInt(14, 'BRL'),
            Coin::fromInt(14, 'BRL'),
            Coin::fromInt(14, 'BRL'),
            Coin::fromInt(14, 'BRL'),
        ], $coin->allocateInstallments(7));
    }

    public function test_allocate_10000_coins_to_11()
    {
        $coin = Coin::fromInt(100_00, 'BRL');

        $this->assertEquals([
            Coin::fromInt(9_10, 'BRL'),
            Coin::fromInt(9_09, 'BRL'),
            Coin::fromInt(9_09, 'BRL'),
            Coin::fromInt(9_09, 'BRL'),
            Coin::fromInt(9_09, 'BRL'),
            Coin::fromInt(9_09, 'BRL'),
            Coin::fromInt(9_09, 'BRL'),
            Coin::fromInt(9_09, 'BRL'),
            Coin::fromInt(9_09, 'BRL'),
            Coin::fromInt(9_09, 'BRL'),
            Coin::fromInt(9_09, 'BRL'),
        ], $coin->allocateInstallments(11));
    }

    public function test_allocate_100_coins_to_0()
    {
        $coin = Coin::fromInt(100, 'BRL');

        $this->expectException(\RangeException::class);
        $coin->allocateInstallments(0);
    }

    public function test_allocate_100_coins_to_negative()
    {
        $coin = Coin::fromInt(100, 'BRL');

        $this->expectException(\RangeException::class);
        $coin->allocateInstallments(-1);
    }

    public function test_allocate_0_coins_to_3()
    {
        $coin = Coin::fromInt(0, 'BRL');

        $this->assertEquals([
            Coin::fromInt(0, 'BRL'),
            Coin::fromInt(0, 'BRL'),
            Coin::fromInt(0, 'BRL'),
        ], $coin->allocateInstallments(3));
    }

    public function test_first_installment_of_3()
    {
        $coin = Coin::fromInt(100, 'BRL');

        $this->assertEquals(Coin::fromInt(34, 'BRL'), $coin->firstInstallment(3));
    }

    public function test_other_installments_of_3()
    {
        $coin = Coin::fromInt(100, 'BRL');

        $this->assertEquals(Coin::fromInt(33, 'BRL'), $coin->otherInstallments(3));
    }


}
