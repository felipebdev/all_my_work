<?php

namespace Tests\Unit\Services\Pagarme\Calculator;

use App\Services\Pagarme\Calculator\AnticipationCalculator;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class AnticipationCalculatorTest extends TestCase
{
    private AnticipationCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->calculator = new AnticipationCalculator();
    }

    public function percentProvider()
    {
        // Anticipation date , amount (in centavos), installments, expected total tax (in centavos)
        return [
            ['10/06/2022', 12339_66, 10, 85904],
            //['19/11/21', 44395.06, 3, 462_89], // 1,02%
            //['29/11/2021', 31254.92, 12, 179060], // 1,02%
            ['15/03/2022', 409_00, 12, 3491],
            ['04/07/2022', 6867_04, 2, 5567],
            ['07/02/2022', 307_94, 12, 2591],
            ['11/02/2022', 409_00, 12, 3441],
            ['07/02/2022', 307_94, 12, 2591],
        ];
    }

    /**
     * @dataProvider percentProvider
     */
    public function test_example($date, $amount, $installments, $expected)
    {
        $carbonDate = Carbon::createFromFormat('d/m/Y', $date);

        $this->assertEquals($expected, $this->calculator->calculateAnticipation($carbonDate, $amount, $installments));
    }
}
