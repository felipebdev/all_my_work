<?php

namespace Modules\Integration\Requests\Rules;

use Illuminate\Validation\Rule;
use Modules\Integration\Enums\CodeEnum;
use Modules\Integration\Enums\TypeEnum;
use Symfony\Component\HttpFoundation\ParameterBag as Request;

class StoreSmartnotasRule extends BaseRule
{
    public function getRules(Request $request) {
        return [
            'description' => 'max:190',
            'is_active' => 'boolean',
            'code' => ['required', Rule::in([CodeEnum::SMARTNOTAS])],
            'type' => ['required', Rule::in([TypeEnum::SMARTNOTAS])],
            'api_webhook' => 'required|url',
            'metadata.process_after_days' => 'required|digits_between:0,90',
        ];
    }

    public function getAttributes() {
        return [
            'description' => 'nome da integração',
            'is_active' => 'ativo',
            'api_webhook' => 'link do webhook smartnotas',
            'metadata.process_after_days' => 'garantia',
        ];
    }

    public function appendData() {
        return [
        ];
    }
}