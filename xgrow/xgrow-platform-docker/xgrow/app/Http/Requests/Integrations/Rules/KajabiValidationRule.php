<?php

namespace App\Http\Requests\Integrations\Rules;

use Illuminate\Validation\Rule;
use App\Http\Requests\Integrations\Rules\BaseRule;

class KajabiValidationRule extends BaseRule {

    public function getRules() {
        return [
            'name_integration' => 'required|max:190',
            'flag_enable' => 'boolean',
            'id_integration' => ['required', Rule::in(['14'])],
            'id_webhook' => ['required', Rule::in(['14'])],
            'email_client' => 'required|email',
            'events' => 'present|array'
        ];
    }

    public function getAttributes() {
        return [
            'name_integration' => 'nome da integração',
            'flag_enable' => 'ativo',
            'id_integration' => 'id da integração',
            'id_webhook' => 'id da integração',
            'email_client' => 'e-mail da conta kajabi',
            'events' => 'quais eventos a integração será acionada'
        ];
    }
}