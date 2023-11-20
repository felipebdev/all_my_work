<?php

namespace Modules\Integration\Requests\Rules;

use Illuminate\Validation\Rule;
use Modules\Integration\Enums\CodeEnum;
use Modules\Integration\Enums\TypeEnum;
use Symfony\Component\HttpFoundation\ParameterBag as Request;

class StoreMauticRule extends BaseRule
{
    public function getRules(Request $request) {
        return [
            'description' => 'max:190',
            'is_active' => 'boolean',
            'code' => ['required', Rule::in([CodeEnum::MAUTIC])],
            'type' => ['required', Rule::in([TypeEnum::MAUTIC])],
            'api_webhook' => ['required'],
            'api_account' => ['required'],
            'api_key' => ['required'],
        ];
    }

    public function getAttributes() {
        return [
            'description' => 'nome da integração',
            'is_active' => 'ativo',
            'api_webhook' => 'URL da conta Mautic',
            'api_account' => 'email da conta Mautic',
            'api_key' => 'senha da conta Mautic',
        ];
    }

    public function appendData() {
        return [
        ];
    }
}