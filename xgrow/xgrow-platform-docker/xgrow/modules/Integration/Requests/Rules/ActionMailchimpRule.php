<?php

namespace Modules\Integration\Requests\Rules;

use Symfony\Component\HttpFoundation\ParameterBag as Request;

class ActionMailchimpRule extends BaseRule
{
    public function getRules(Request $request)
    {
        return [
            'metadata.list' => 'required',
            'metadata.tags' => 'array'
        ];
    }

    public function getAttributes()
    {
        return [
            'metadata.list' => 'lista',
            'metadata.tags' => 'tags'
        ];
    }

    public function appendData() {
        return [
        ];
    }
}
