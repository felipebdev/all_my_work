<?php

namespace App\Http\Requests\Integrations\Rules;

use Illuminate\Validation\Rule;
use App\Http\Requests\Integrations\Rules\BaseRule;

class EduzzValidationRule extends BaseRule {

    public function getRules() {
        return [
            'name_integration' => 'required|max:190',
            'days_limit_payment_pendent' => 'required|integer',
            'source_token' => 'required',
            'flag_enable' => 'boolean',
            'trigger_email' => 'boolean',
            'id_integration' => ['required', Rule::in(['6'])],
            'id_webhook' => ['required', Rule::in(['6'])],
        ];
    }

    public function getAttributes() {
        return [
            'name_integration' => 'nome da integração',
            'days_limit_payment_pendent' => 'dias limite de pagamento pendente',
            'source_token' => 'token eduzz',
            'flag_enable' => 'ativo',
            'id_integration' => 'id da integração',
            'id_webhook' => 'id da integração'
        ];
    }
}