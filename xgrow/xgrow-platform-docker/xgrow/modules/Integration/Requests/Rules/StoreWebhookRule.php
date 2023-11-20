<?php

namespace Modules\Integration\Requests\Rules;

use App\Rules\UrlSsl;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Modules\Integration\Enums\CodeEnum;
use Modules\Integration\Enums\TypeEnum;
use Symfony\Component\HttpFoundation\ParameterBag as Request;

class StoreWebhookRule extends BaseRule
{
    public function getRules(Request $request) {
        return [
            'description' => 'max:190',
            'is_active' => 'boolean',
            'code' => ['required', Rule::in([CodeEnum::WEBHOOK])],
            'type' => ['required', Rule::in([TypeEnum::WEBHOOK])],
            'api_webhook' => ['required', new UrlSsl],
        ];
    }

    public function getAttributes() {
        return [
            'description' => 'nome da integração',
            'is_active' => 'ativo',
            'api_webhook' => 'url do webhook',
        ];
    }

    public function appendData() {
        return [
            'api_key' => generateToken(Auth::user()->platform_id)
        ];
    }
}