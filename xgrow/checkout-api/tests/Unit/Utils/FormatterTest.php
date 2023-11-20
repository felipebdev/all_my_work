<?php

namespace Tests\Unit\Utils;

use App\Utils\Formatter;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

class FormatterTest extends TestCase
{
    /**
     * @dataProvider cnpjOkProvider
     */
    public function test_cnpj_ok($value, $expected)
    {
        $formatted = Formatter::cnpj($value);
        $this->assertEquals($expected, $formatted);
    }

    /**
     * @dataProvider cnpjNotOkProvider
     */
    public function test_cnpj_not_ok($value)
    {
        $this->expectException(UnexpectedValueException::class);
        $x = Formatter::cnpj($value);
        dump($x);
    }

    /**
     * @dataProvider cpfOkProvider
     */
    public function test_cpf_ok($value, $expected)
    {
        $formatted = Formatter::cpf($value);
        $this->assertEquals($expected, $formatted);
    }

    /**
     * @dataProvider cpfNotOkProvider
     */
    public function test_cpf_not_ok($value)
    {
        $this->expectException(UnexpectedValueException::class);
        Formatter::cpf($value);
    }

    /**
     * @dataProvider cnpjAndCpfProvider
     */
    public function test_document_ok($value, $expected)
    {
        $formatted = Formatter::documentNumber($value);
        $this->assertEquals($expected, $formatted);
    }

    /**
     * @dataProvider cepOkProvider
     */
    public function test_cep_ok($value, $expected)
    {
        $formatted = Formatter::cep($value);
        $this->assertEquals($expected, $formatted);
    }

    /**
     * @dataProvider cepNotOkProvider
     */
    public function test_cep_not_ok($value)
    {
        $this->expectException(UnexpectedValueException::class);
        Formatter::cep($value);
    }

    /**
     * @dataProvider brPhoneMobileOkProvider
     */
    public function test_br_phone_mobile_ok($value, $expected)
    {
        $formatted = Formatter::brPhoneMobile($value);
        $this->assertEquals($expected, $formatted);
    }

    /**
     * @dataProvider brPhonneMobileNotOkProvider
     */
    public function test_br_phone_mobile_not_ok($value)
    {
        $this->expectException(UnexpectedValueException::class);
        Formatter::brPhoneMobile($value);
    }

    /**
     * @dataProvider brPhoneLandlineOkProvider
     */
    public function test_br_phone_landline_ok($value, $expected)
    {
        $formatted = Formatter::brPhoneLandline($value);
        $this->assertEquals($expected, $formatted);
    }

    /**
     * @dataProvider brPhoneLandlineNotOkProvider
     */
    public function test_br_phone_landline_not_ok($value)
    {
        $this->expectException(UnexpectedValueException::class);
        Formatter::brPhoneLandline($value);
    }

    public function cpfOkProvider()
    {
        return [
            ['42088766898', '420.887.668-98'],
            ['470.822.268-88', '470.822.268-88'], // formatted
            ['512 708 253 56', '512.708.253-56'], // with spaces
            [' 027.346.268-70 ', '027.346.268-70'], // with trailing spaces
            [' 442 334 667 23 ', '442.334.667-23'], // spaces + trailing
            ['165-525-463-43', '165.525.463-43'], // weird formatting
            ['x23a632f826u714', '236.328.267-14'], // with trash
        ];
    }

    public function cpfNotOkProvider()
    {
        return [
            ['561.533.771-0'], // formatted, short
            ['704.035.355-571'], // formatted, long
            ['1141258110'], // clean, short
            ['478014870791'], // clean, long
            ['a61220807'], // trashed, short
            ['t527x860t433680a'], // trashed, long
        ];
    }

    public function cnpjOkProvider()
    {
        return [
            ['41375544000115', '41.375.544/0001-15'],
            ['75.083.550/0001-10', '75.083.550/0001-10'], // formatted
            ['21 487 730 0001 39', '21.487.730/0001-39'], // with spaces
            [' 63.306.278/0001-01 ', '63.306.278/0001-01'], // with trailing spaces
            ['32.836.663_0001-37', '32.836.663/0001-37'], // weird formatting
            ['25dfdsa570fdsa373j0001i29', '25.570.373/0001-29'], // with trash
        ];
    }

    public function cnpjAndCpfProvider()
    {
        return array_merge($this->cnpjOkProvider(), $this->cpfOkProvider());
    }

    public function cnpjNotOkProvider()
    {
        return [
            ['4137554400011'], // short
            ['750835500001101'], // long
            ['21x487a73010x00x1'], // trashed, short
            ['63b3f06f27l0001i0121'], // trashed, long
        ];
    }

    public function cepOkProvider()
    {
        return [
            ['56909494', '56909-494'],
            ['60337-040', '60337-040'], // formatted
            ['78 705-340', '78705-340'], // with spaces
            [' 63500-200 ', '63500-200'], // with trailing spaces
            ['63.500_200', '63500-200'], // weird formatting
            ['a635n00-200b', '63500-200'], // with trash
        ];
    }

    public function cepNotOkProvider()
    {
        return [
            ['5690949'], // short
            ['569094944'], // long
            ['56a90x94l9'], // trashed, short
            ['5690x949u44z'], // trashed, long
        ];
    }

    public function brPhoneMobileOkProvider()
    {
        return [
            ['60964892710', '(60) 96489-2710'],
            ['(37)95344-4511', '(37) 95344-4511'], // formatted
            ['42 99739 3182', '(42) 99739-3182'], // with spaces
            [' (52)96578-5432 ', '(52) 96578-5432'], // with trailing spaces
            ['26.99584_7703', '(26) 99584-7703'], // weird formatting
            ['42a904x9542y59', '(42) 90495-4259'], // with trash
        ];
    }

    public function brPhonneMobileNotOkProvider()
    {
        return [
            ['6096492710'], // short
            ['374953444511'], // long
            ['429973x9318'], // trashed, short
            ['52a965x78543n24'], // trashed, long
        ];
    }

    public function brPhoneLandlineOkProvider()
    {
        return [
            ['6064892710', '(60) 6489-2710'],
            ['(37)5344-4511', '(37) 5344-4511'], // formatted
            ['42 9739 3182', '(42) 9739-3182'], // with spaces
            [' (52)6578-5432 ', '(52) 6578-5432'], // with trailing spaces
            ['26.9584_7703', '(26) 9584-7703'], // weird formatting
            ['42a04x9542y59', '(42) 0495-4259'], // with trash
        ];
    }

    public function brPhoneLandlineNotOkProvider()
    {
        return [
            ['126049271'], // short
            ['11374344451'], // long
            ['21373x931x8'], // trashed, short
            ['52a65x78543n24'], // trashed, long
        ];
    }
}
