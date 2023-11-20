<?php

namespace Modules\Integration\Requests\Rules;

use Symfony\Component\HttpFoundation\ParameterBag as Request;

class ActionPipedriveRule extends BaseRule
{
    public function getRules(Request $request)
    {
        return [];
    }

    public function getAttributes()
    {
        return [];
    }

    public function appendData() {
        return [
        ];
    }
}
