<?php

namespace App\Http\Requests\Integrations\Rules;

use Illuminate\Validation\Rule;
use App\Http\Requests\Integrations\Rules\BaseRule;

class DigitalmanagerguruValidationRule extends BaseRule {

    public function getRules() {
        return [
            'name_integration' => 'required|max:190',
            'flag_enable' => 'boolean',
            'id_integration' => ['required'],
            'id_webhook' => ['required'],
            'digitalmanagerguru_api_key' => 'required',
        ];
    }

    public function getAttributes() {
        return [
            'name_integration' => 'nome da integração',
            'flag_enable' => 'ativo',
            'id_integration' => 'id da integração',
            'id_webhook' => 'id da integração',
            'digitalmanagerguru_api_key' =>  'api key digital manager'
        ];
    }
}
