<?php

namespace Modules\Integration\Requests\Rules;

use Symfony\Component\HttpFoundation\ParameterBag as Request;

class ActionActivecampaignRule extends BaseRule
{
    public function getRules(Request $request)
    {
        return [
            'metadata.list' => 'required_without:metadata.tags',
            'metadata.tags' => 'required_without:metadata.list'
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
