<?php

namespace Modules\Integration\Requests\Rules;

use Modules\Integration\Rules\MailchimpToken;
use Illuminate\Validation\Rule;
use Modules\Integration\Enums\CodeEnum;
use Modules\Integration\Enums\TypeEnum;
use Symfony\Component\HttpFoundation\ParameterBag as Request;

class StoreMailchimpRule extends BaseRule
{
    public function getRules(Request $request) {
        return [
            'description' => 'max:190',
            'is_active' => 'boolean',
            'code' => ['required', Rule::in([CodeEnum::MAILCHIMP])],
            'type' => ['required', Rule::in([TypeEnum::MAILCHIMP])],
            'api_key' => ['required', 'regex:/-/i'], //Ex: 0123456789abcdef0123456789abcde-us6
        ];
    }

    public function getAttributes() {
        return [
            'description' => 'nome da integração',
            'is_active' => 'ativo',
            'api_key' => 'chave da api mailchimp',
        ];
    }

    public function appendData() {
        return [
        ];
    }
}