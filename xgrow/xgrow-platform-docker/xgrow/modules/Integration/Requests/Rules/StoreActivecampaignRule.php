<?php

namespace Modules\Integration\Requests\Rules;

use Illuminate\Validation\Rule;
use Modules\Integration\Enums\CodeEnum;
use Modules\Integration\Enums\TypeEnum;
use Symfony\Component\HttpFoundation\ParameterBag as Request;

class StoreActivecampaignRule extends BaseRule
{
    public function getRules(Request $request) {
        return [
            'description' => 'max:190',
            'is_active' => 'boolean',
            'code' => ['required', Rule::in([CodeEnum::ACTIVECAMPAIGN])],
            'type' => ['required', Rule::in([TypeEnum::ACTIVECAMPAIGN])],
            'api_webhook' => 'required|url',
            'api_key' => 'required',
        ];
    }

    public function getAttributes() {
        return [
            'description' => 'nome da integração',
            'is_active' => 'ativo',
            'api_webhook' => 'url da api activecampaign',
            'api_key' => 'chave da api activecampaign',
        ];
    }

    public function appendData() {
        return [
        ];
    }
}