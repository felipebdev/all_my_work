<?php

namespace Modules\Integration\Requests\Rules;

use Symfony\Component\HttpFoundation\ParameterBag as Request;

abstract class BaseRule
{
    public abstract function getRules(Request $request);
    public abstract function getAttributes();
    public abstract function appendData();
}
