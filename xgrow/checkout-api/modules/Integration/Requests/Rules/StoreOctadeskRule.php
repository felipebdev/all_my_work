<?php

namespace Modules\Integration\Requests\Rules;

use Illuminate\Validation\Rule;
use Modules\Integration\Enums\CodeEnum;
use Modules\Integration\Enums\TypeEnum;
use Symfony\Component\HttpFoundation\ParameterBag as Request;

class StoreOctadeskRule extends BaseRule
{
    public function getRules(Request $request) {
        return [
            'description' => 'max:190',
            'is_active' => 'boolean',
            'code' => ['required', Rule::in([CodeEnum::OCTADESK])],
            'type' => ['required', Rule::in([TypeEnum::OCTADESK])],
            'api_account' => 'required|email',
            'api_key' => 'required',
        ];
    }

    public function getAttributes() {
        return [
            'description' => 'nome da integração',
            'is_active' => 'ativo',
            'api_account' => 'email da conta octadesk',
            'api_key' => 'chave da api octadesk',
        ];
    }

    public function appendData() {
        return [
        ];
    }
}