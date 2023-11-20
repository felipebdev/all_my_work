<?php

namespace App\Services\Objects;

abstract class BaseFilter
{
    public function getData(){
        return json_encode((array) $this);
    }
}
