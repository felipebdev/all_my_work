<?php

namespace Tests\Unit\Services\Mundipagg\Objects;

use App\Services\Mundipagg\Objects\RecipientData;
use PHPUnit\Framework\TestCase;

class RecipientDataTest extends TestCase
{

    /**
     * @covers \App\Services\Mundipagg\Objects\RecipientData::cleanSubstr
     */
    public function test_clean_substr_common_cases()
    {
        $recipientData = new RecipientData();

        // only digits
        $result = $this->invokeMethod($recipientData, 'cleanSubstr', ['1a2b3c4d5e']);
        $this->assertSame('12345', $result);

        // offset = 2 : (2 from left to right) > 12[345
        $result = $this->invokeMethod($recipientData, 'cleanSubstr', ['1a2b3c4d5e', 2]);
        $this->assertSame('345', $result);

        // offset = -2 (from right to left) : 123[45
        $result = $this->invokeMethod($recipientData, 'cleanSubstr', ['1a2b3c4d5e', -2]);
        $this->assertSame('45', $result);

        // offset = 1, limit = 2 : 1[23]45
        $result = $this->invokeMethod($recipientData, 'cleanSubstr', ['1a2b3c4d5e', 1, 2]);
        $this->assertSame('23', $result);

        // offset = -4 (from right to left), limit = 2 : 1[23]45
        $result = $this->invokeMethod($recipientData, 'cleanSubstr', ['1a2b3c4d5e', -4, 2]);
        $this->assertSame('23', $result);
    }

    /**
     * @covers \App\Services\Mundipagg\Objects\RecipientData::cleanSubstr
     */
    public function test_clean_substr_empty_cases()
    {
        $recipientData = new RecipientData();
        $result = $this->invokeMethod($recipientData, 'cleanSubstr', ['abc']);
        $this->assertSame('', $result);

        $result = $this->invokeMethod($recipientData, 'cleanSubstr', ['']);
        $this->assertSame('', $result);

        $result = $this->invokeMethod($recipientData, 'cleanSubstr', [null]);
        $this->assertSame('', $result);
    }

    /**
     * @covers \App\Services\Mundipagg\Objects\RecipientData::cleanSubstr
     */
    public function test_clean_substr_return_false()
    {
        $recipientData = new RecipientData();
        $result = $this->invokeMethod($recipientData, 'cleanSubstr', ['', 2]);
        $this->assertFalse($result);

        $result = $this->invokeMethod($recipientData, 'cleanSubstr', ['1', 2]);
        $this->assertFalse($result);

        $result = $this->invokeMethod($recipientData, 'cleanSubstr', ['abc', 1]);
        $this->assertFalse($result);
    }

    /**
     * Call protected/private method of a class.
     *
     * @param  object &$object  Instantiated object that we will run method on.
     * @param  string  $methodName  Method name to call
     * @param  array  $parameters  Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

}
