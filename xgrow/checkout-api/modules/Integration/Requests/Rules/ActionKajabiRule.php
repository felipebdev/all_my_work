<?php

namespace Modules\Integration\Requests\Rules;

use Symfony\Component\HttpFoundation\ParameterBag as Request;

class ActionKajabiRule extends BaseRule
{
    public function getRules(Request $request)
    {
        return [
            'metadata.product_webhook' => 'required',
        ];
    }

    public function getAttributes()
    {
        return [
            'metadata.product_webhook' => 'url do webhook',
        ];
    }

    public function appendData() {
        return [
        ];
    }
}
