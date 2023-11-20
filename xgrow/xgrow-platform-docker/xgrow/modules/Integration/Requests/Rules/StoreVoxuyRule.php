<?php

namespace Modules\Integration\Requests\Rules;

use Illuminate\Validation\Rule;
use Modules\Integration\Enums\CodeEnum;
use Modules\Integration\Enums\TypeEnum;
use Symfony\Component\HttpFoundation\ParameterBag as Request;

class StoreVoxuyRule extends BaseRule
{
    public function getRules(Request $request) {
        return [
            'description' => 'max:190',
            'is_active' => 'boolean',
            'code' => ['required', Rule::in([CodeEnum::VOXUY])],
            'type' => ['required', Rule::in([TypeEnum::VOXUY])],
            'api_webhook' => 'required|url',
            'api_key' => 'required',
            'metadata.planId' => 'required',
        ];
    }

    public function getAttributes() {
        return [
            'description' => 'nome da integração',
            'is_active' => 'ativo',
            'api_webhook' => 'link do webhook voxuy',
            'api_key' => 'chave de api voxuy',
            'metadata.planId' => 'id do plano',
        ];
    }

    public function appendData() {
        return [
        ];
    }
}
