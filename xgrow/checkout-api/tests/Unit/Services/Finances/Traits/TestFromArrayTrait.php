<?php

namespace Tests\Unit\Services\Finances\Traits;

use App\Services\Finances\Contracts\FromArrayInterface;
use App\Services\Finances\Traits\FromArrayTrait;
use PHPUnit\Framework\TestCase;

class UsingSetter implements FromArrayInterface
{
    use FromArrayTrait;

    public function withTestValue($value)
    {
        $this->testValue = $value * 2;
    }

    public int $testValue;
}

class UsingProperty implements FromArrayInterface
{
    use FromArrayTrait;

    public int $testValue;
}

class UsingCamel implements FromArrayInterface
{
    use FromArrayTrait;

    public int $testValue;
}

class TestFromArrayTrait extends TestCase
{

    public function test_using_setter()
    {
        $object = UsingSetter::fromArray(['testValue' => 3]);
        $this->assertEquals(6, $object->testValue);
    }

    public function test_using_exact_property()
    {
        $object = UsingProperty::fromArray(['testValue' => 3]);
        $this->assertEquals(3, $object->testValue);
    }

    public function test_using_camel_case()
    {
        $object = UsingCamel::fromArray(['test_value' => 3]);
        $this->assertEquals(3, $object->testValue);
    }

    public function test_setter_first()
    {
        $object = UsingSetter::fromArray(['testValue' => 3]);
        $this->assertEquals(6, $object->testValue);
    }

}
