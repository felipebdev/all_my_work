<?php

namespace Modules\Integration\Requests\Rules;

use Illuminate\Validation\Rule;
use Modules\Integration\Enums\CodeEnum;
use Modules\Integration\Enums\TypeEnum;
use Symfony\Component\HttpFoundation\ParameterBag as Request;

class StoreHubspotRule extends BaseRule
{
    public function getRules(Request $request)
    {
        return [
            'description' => 'max:190',
            'is_active' => 'boolean',
            'code' => ['required', Rule::in([CodeEnum::HUBSPOT])],
            'type' => ['required', Rule::in([TypeEnum::HUBSPOT])],
            'api_key' => 'required|uuid',
        ];
    }

    public function getAttributes()
    {
        return [
            'description' => 'nome da integração',
            'is_active' => 'ativo',
            'api_key' => 'chave da API HubSpot',
        ];
    }

    public function appendData()
    {
        return [
        ];
    }
}
