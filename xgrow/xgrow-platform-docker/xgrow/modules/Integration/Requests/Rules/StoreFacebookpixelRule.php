<?php

namespace Modules\Integration\Requests\Rules;

use Illuminate\Validation\Rule;
use Modules\Integration\Enums\CodeEnum;
use Modules\Integration\Enums\TypeEnum;
use Symfony\Component\HttpFoundation\ParameterBag as Request;

class StoreFacebookpixelRule extends BaseRule
{
    public function getRules(Request $request) {
        return [
            'description' => 'required|max:190',
            'is_active' => 'boolean',
            'api_account' => 'required',
            'api_key' => 'required',
            'metadata.payment_method' => 'required',
        ];
    }

    public function getAttributes() {
        return [
            'description' => 'nome da integração',
            'is_active' => 'ativo',
            'api_account' => 'ID do pixel Facebook',
            'api_key' => 'Token de acesso',
            'metadata.payment_method' => 'Receber confirmação de venda de qual meio de pagamento?'
        ];
    }

    public function appendData() {
        return [
        ];
    }
}
