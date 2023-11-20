<?php

namespace Modules\Integration\Requests\Rules;

use Symfony\Component\HttpFoundation\ParameterBag as Request;

class ActionLeadloversRule extends BaseRule
{
    public function getRules(Request $request)
    {
        return [
            'metadata.machineCode' => 'required',
            'metadata.sequenceCode' => 'required',
            'metadata.levelCode' => 'required',
            'metadata.tags' => 'array'
        ];
    }

    public function getAttributes()
    {
        return [
            'metadata.machineCode' => 'machineCode',
            'metadata.sequenceCode' => 'sequenceCode',
            'metadata.levelCode' => 'levelCode',
            'metadata.tags' => 'tags'
        ];
    }

    public function appendData() {
        return [
        ];
    }
}
