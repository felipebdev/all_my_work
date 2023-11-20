<?php

namespace Tests\Unit;

use App\Utils\XorIntObfuscator;
use PHPUnit\Framework\TestCase;

class XorIntObfuscatorTest extends TestCase
{

    /**
     * @dataProvider intProvider
     */
    public function test_encode_decode($value)
    {
        $encoded = XorIntObfuscator::obfuscate($value);

        $this->assertEquals($value, XorIntObfuscator::reveal($encoded));
    }

    public function test_validation()
    {
        // some invalid values
        $this->assertFalse(XorIntObfuscator::validate(2));
        $this->assertFalse(XorIntObfuscator::validate(134));
        $this->assertFalse(XorIntObfuscator::validate(45678));
        $this->assertFalse(XorIntObfuscator::validate(34897564342343245));
    }

    public function intProvider()
    {
        foreach (range(10, 30) as $testNumber) {
            yield [rand()];
        }
    }

}
