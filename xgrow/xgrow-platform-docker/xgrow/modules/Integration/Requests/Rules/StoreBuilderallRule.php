<?php

namespace Modules\Integration\Requests\Rules;

use Illuminate\Validation\Rule;
use Modules\Integration\Enums\CodeEnum;
use Modules\Integration\Enums\TypeEnum;
use Symfony\Component\HttpFoundation\ParameterBag as Request;

class StoreBuilderallRule extends BaseRule
{
    public function getRules(Request $request) {
        return [
            'description' => 'max:190',
            'is_active' => 'boolean',
            'code' => ['required', Rule::in([CodeEnum::BUILDERALL])],
            'type' => ['required', Rule::in([TypeEnum::BUILDERALL])],
            'api_key' => 'required',
        ];
    }

    public function getAttributes() {
        return [
            'description' => 'Nome da integração',
            'is_active' => 'Ativo',
            'api_key' => 'Chave da api BuilderAll/MailingBoss',
        ];
    }

    public function appendData() {
        return [
        ];
    }
}
