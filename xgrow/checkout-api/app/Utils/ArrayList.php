<?php

namespace App\Utils;

use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;
use UnexpectedValueException;

class ArrayList implements ArrayAccess, IteratorAggregate
{
    protected $type;
    protected $items = [];

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function offsetSet($key, $value)
    {
        if ($value instanceof $this->type) {
            $key ? $this->items[$key] = $value : array_push($this->items, $value);
            return $this;
        }

        throw new UnexpectedValueException("This list only accepts {$this->type}");
    }

    public function offsetGet($key)
    {
        return $this->items[$key];
    }

    public function offsetExists($key)
    {
        return isset($this->items[$key]);
    }

    public function offsetUnset($key)
    {
        unset($this->items[$key]);
    }

    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }
}
