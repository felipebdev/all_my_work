<?php

namespace Modules\Integration\Requests\Rules;

use Illuminate\Validation\Rule;
use Modules\Integration\Enums\CodeEnum;
use Modules\Integration\Enums\TypeEnum;
use Symfony\Component\HttpFoundation\ParameterBag as Request;

class StoreLeadloversRule extends BaseRule
{
    public function getRules(Request $request) {
        return [
            'description' => 'max:190',
            'is_active' => 'boolean',
            'code' => ['required', Rule::in([CodeEnum::LEADLOVERS])],
            'type' => ['required', Rule::in([TypeEnum::LEADLOVERS])],
            'api_key' => ['required'],
        ];
    }

    public function getAttributes() {
        return [
            'description' => 'Nome da Integração',
            'is_active' => 'Ativo',
            'api_key' => 'Chave da API LeadLovers',
        ];
    }

    public function appendData() {
        return [
        ];
    }
}