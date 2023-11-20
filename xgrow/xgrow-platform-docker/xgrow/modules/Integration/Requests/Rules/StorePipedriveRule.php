<?php

namespace Modules\Integration\Requests\Rules;

use App\Rules\UrlSsl;
use Illuminate\Validation\Rule;
use Modules\Integration\Enums\CodeEnum;
use Modules\Integration\Enums\TypeEnum;
use Symfony\Component\HttpFoundation\ParameterBag as Request;

class StorePipedriveRule extends BaseRule
{
    public function getRules(Request $request) {
        return [
            'description' => 'max:190',
            'is_active' => 'boolean',
            'code' => ['required', Rule::in([CodeEnum::PIPEDRIVE])],
            'type' => ['required', Rule::in([TypeEnum::PIPEDRIVE])],
            'api_account' => ['required'],
            'api_key' => ['required'],
        ];
    }

    public function getAttributes() {
        return [
            'description' => 'nome da integração',
            'is_active' => 'ativo',
            'api_account' => 'domínio do pipedrive',
            'api_key' => 'chave da api pipedrive',
        ];
    }

    public function appendData() {
        return [
        ];
    }
}