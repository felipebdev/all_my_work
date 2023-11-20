<?php

namespace App\Http\Requests\Integrations\Rules;

use Illuminate\Validation\Rule;
use App\Http\Requests\Integrations\Rules\BaseRule;

class GoogleadsValidationRule extends BaseRule {

    public function getRules() {
        return [
            'name_integration' => 'required|max:190',
            'ads_id' => 'required',
            'flag_enable' => 'boolean',
            'id_integration' => ['required', Rule::in(['10'])],
            'id_webhook' => ['required', Rule::in(['10'])],
            'infos.ads_conversion_label' => 'required'
        ];
    }

    public function getAttributes() {
        return [
            'name_integration' => 'nome da integração',
            'ads_id' => 'id do pixel adwords',
            'flag_enable' => 'ativo',
            'id_integration' => 'id da integração',
            'id_webhook' => 'id da integração',
            'infos.ads_conversion_label' => 'label de conversão do adwords'
        ];
    }
}