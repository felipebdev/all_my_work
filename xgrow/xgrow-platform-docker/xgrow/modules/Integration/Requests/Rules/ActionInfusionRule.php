<?php

namespace Modules\Integration\Requests\Rules;

use Symfony\Component\HttpFoundation\ParameterBag as Request;

class ActionInfusionRule extends BaseRule
{
    public function getRules(Request $request)
    {
        return [
            'metadata.tags' => 'required'
        ];
    }

    public function getAttributes()
    {
        return [
            'metadata.tags' => 'tags'
        ];
    }

    public function appendData() {
        return [
        ];
    }
}
