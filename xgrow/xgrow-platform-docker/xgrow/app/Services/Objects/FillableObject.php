<?php

namespace App\Services\Objects;

abstract class FillableObject
{
    public function __construct(?array $data = [])
    {
        if (!is_null($data)) {
            $this->fill($data);
        }
    }

    /**
     * Fill object attributes with data from array if setter or attribute is defined
     *
     * If a setter is defined (eg: setVariableName($var)) it will invoke the method passing value as parameter
     * If a setter is not defined, it will check if property exists and set it directly (variable types MUST be
     * compatible with class declaration)
     *
     * @param  array  $data
     */
    public function fill(array $data = [])
    {
        foreach ($data as $key => $value) {
            if ($this->fillBySetter($key, $value)) {
                continue;
            }

            if ($this->fillByProperty($key, $value)) {
                continue;
            }
        }
    }

    /**
     * Looks for a setter and invoke method if exists
     *
     * @param $key
     * @param $value
     * @return bool ture if setter exists, false otherwise
     */
    private function fillBySetter($key, $value): bool
    {
        $name = ucfirst($key);
        $methodName = "set{$name}";

        if (method_exists($this, $methodName)) {
            $this->$methodName($value);
            return true;
        }

        return false;
    }

    /**
     * Looks for a property and set if exists
     *
     * @param $key
     * @param $value
     * @return bool true if property exists, false otherwise
     */
    private function fillByProperty($key, $value): bool
    {
        if (property_exists($this, $key)) {
            $this->{$key} = $value;
            return true;
        }

        return false;
    }
}
