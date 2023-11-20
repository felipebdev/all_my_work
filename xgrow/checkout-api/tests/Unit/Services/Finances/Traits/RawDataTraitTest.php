<?php

namespace Tests\Unit\Services\Finances\Traits;

use App\Services\Finances\Contracts\SavesRawData;
use App\Services\Finances\Traits\RawDataTrait;
use PHPUnit\Framework\TestCase;

class ConcreteRaw implements SavesRawData
{
    use RawDataTrait;
}

class RawDataTraitTest extends TestCase
{

    public function test_array_data()
    {
        $object = new ConcreteRaw();

        $object->setRawData(['testValue' => 3]);

        $this->assertSame(['testValue' => 3], $object->getRawData());
    }


    public function test_object_data()
    {
        $object = new ConcreteRaw();

        $object->setRawData((object) ['testValue' => 3]);

        $this->assertEqualsCanonicalizing((object) ['testValue' => 3], $object->getRawData());
    }

    public function test_string_type_data()
    {
        $object = new ConcreteRaw();

        $object->setRawData('raw data');

        $this->assertSame('raw data', $object->getRawData());
    }

    public function test_null_data()
    {
        $object = new ConcreteRaw();

        $object->setRawData(null);

        $this->assertSame(null, $object->getRawData());
    }

    public function test_int_data()
    {
        $object = new ConcreteRaw();

        $object->setRawData(3);

        $this->assertSame(3, $object->getRawData());
    }

    public function test_float_data()
    {
        $object = new ConcreteRaw();

        $object->setRawData(3.14);

        $this->assertSame(3.14, $object->getRawData());
    }

    public function test_numeric_string_data()
    {
        $object = new ConcreteRaw();

        $object->setRawData('3.14');

        $this->assertSame('3.14', $object->getRawData());
    }


}
