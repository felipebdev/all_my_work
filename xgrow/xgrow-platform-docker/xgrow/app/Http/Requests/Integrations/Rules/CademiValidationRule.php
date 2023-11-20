<?php

namespace App\Http\Requests\Integrations\Rules;

use Illuminate\Validation\Rule;
use App\Http\Requests\Integrations\Rules\BaseRule;

class CademiValidationRule extends BaseRule {

    public function getRules() {
        return [
            'name_integration' => 'required|max:190',
            'flag_enable' => 'boolean',
            'id_integration' => ['required', Rule::in(['15'])],
            'id_webhook' => ['required', Rule::in(['15'])],
            'api_key' => 'required',
            'url_webhook' => 'required|url',
            'events' => 'present|array'
        ];
    }

    public function getAttributes() {
        return [
            'name_integration' => 'nome da integração',
            'flag_enable' => 'ativo',
            'id_integration' => 'id da integração',
            'id_webhook' => 'id da integração',
            'url_webhook' => 'url da api cademi',
            'api_key' => 'token da api cademi',
            'events' => 'quais eventos a integração será acionada'
        ];
    }
}