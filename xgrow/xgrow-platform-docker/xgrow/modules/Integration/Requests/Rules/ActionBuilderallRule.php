<?php

namespace Modules\Integration\Requests\Rules;

use Symfony\Component\HttpFoundation\ParameterBag as Request;

class ActionBuilderallRule extends BaseRule
{
    public function getRules(Request $request)
    {
        return [
//            'metadata.list' => 'required_without:metadata.tags',
//            'metadata.tags' => 'required_without:metadata.list'
        ];
    }

    public function getAttributes()
    {
        return [
            'metadata.list' => 'Lista de contatos',
            'metadata.tags' => 'Tags'
        ];
    }

    public function appendData() {
        return [
        ];
    }
}
