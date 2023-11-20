<?php

namespace Modules\Integration\Requests\Rules;

use Illuminate\Validation\Rule;
use Modules\Integration\Enums\CodeEnum;
use Modules\Integration\Enums\TypeEnum;
use Symfony\Component\HttpFoundation\ParameterBag as Request;

class StoreKajabiRule extends BaseRule
{
    public function getRules(Request $request) {
        return [
            'description' => 'max:190',
            'is_active' => 'boolean',
            'code' => ['required', Rule::in([CodeEnum::KAJABI])],
            'type' => ['required', Rule::in([TypeEnum::KAJABI])],
            'api_account' => 'required|email',
        ];
    }

    public function getAttributes() {
        return [
            'description' => 'nome da integração',
            'is_active' => 'ativo',
            'api_account' => 'email da conta kajabi',
        ];
    }

    public function appendData() {
        return [
        ];
    }
}