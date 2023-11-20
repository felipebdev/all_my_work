<?php

namespace Modules\Integration\Requests\Rules;

use Illuminate\Validation\Rule;
use Modules\Integration\Enums\CodeEnum;
use Modules\Integration\Enums\TypeEnum;
use Symfony\Component\HttpFoundation\ParameterBag as Request;

class StoreEnotasRule extends BaseRule
{
    public function getRules(Request $request) {
        return [
            'description' => 'max:190',
            'is_active' => 'boolean',
            'code' => ['required', Rule::in([CodeEnum::ENOTAS])],
            'type' => ['required', Rule::in([TypeEnum::ENOTAS])],
            'api_key' => 'required',
            'metadata.process_after_days' => 'required',
        ];
    }

    public function getAttributes() {
        return [
            'description' => 'nome da integração',
            'is_active' => 'ativo',
            'api_key' => 'chave de api do eNotas',
            'metadata.process_after_days' => 'a garantia é necessária',
        ];
    }

    public function appendData() {
        return [
        ];
    }
}
