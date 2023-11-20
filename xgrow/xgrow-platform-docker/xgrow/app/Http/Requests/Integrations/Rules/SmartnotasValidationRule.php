<?php

namespace App\Http\Requests\Integrations\Rules;

use Illuminate\Validation\Rule;
use App\Http\Requests\Integrations\Rules\BaseRule;

class SmartnotasValidationRule extends BaseRule {

    public function getRules() {
        return [
            'name_integration' => 'required|max:190',
            'url_webhook' => 'required|url',
            'flag_enable' => 'boolean',
            'id_integration' => ['required', Rule::in(['11'])],
            'id_webhook' => ['required', Rule::in(['11'])],
            'process_after_days' => 'required|integer',
            'events.on_approve_payment.do_sefaz_doc' => 'required'
        ];
    }

    public function getAttributes() {
        return [
            'name_integration' => 'nome da integração',
            'url_webhook' => 'link do webhook smartnotas',
            'process_after_days' => 'garantia',
            'flag_enable' => 'ativo',
            'id_integration' => 'id da integração',
            'id_webhook' => 'id da integração',
            'events.on_approve_payment.do_sefaz_doc' => 'gerar nota fiscal'
        ];
    }
}
