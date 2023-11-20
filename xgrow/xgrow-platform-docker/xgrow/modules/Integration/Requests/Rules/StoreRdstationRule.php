<?php

namespace Modules\Integration\Requests\Rules;

use Illuminate\Validation\Rule;
use Modules\Integration\Enums\CodeEnum;
use Modules\Integration\Enums\TypeEnum;
use Symfony\Component\HttpFoundation\ParameterBag as Request;

class StoreRdstationRule extends BaseRule
{
    public function getRules(Request $request)
    {
        return [
            'description' => 'max:190',
            'is_active' => 'boolean',
            'code' => ['required', Rule::in([CodeEnum::RDSTATION])],
            'type' => ['required', Rule::in([TypeEnum::RDSTATION])],
            'api_key' => 'required',
        ];
    }

    public function getAttributes()
    {
        return [
            'description' => 'nome da integração',
            'is_active' => 'ativo',
            'api_key' => 'API key da RD Station',
        ];
    }

    public function appendData()
    {
        return [
        ];
    }
}