<?php

namespace App\Http\Requests\Integrations\Rules;

use Illuminate\Validation\Rule;
use App\Http\Requests\Integrations\Rules\BaseRule;

class ActivecampaignValidationRule extends BaseRule {

    public function getRules() {
        return [
            'name_integration' => 'required|max:190',
            'flag_enable' => 'boolean',
            'id_integration' => ['required', Rule::in(['8'])],
            'id_webhook' => ['required', Rule::in(['8'])],
            'url_webhook' => 'required|url',
            'activecampaign_api_key' => 'required',
            'events' => 'present|array'
        ];
    }

    public function getAttributes() {
        return [
            'name_integration' => 'nome da integração',
            'url_webhook' => 'url da api activecampaign',
            'flag_enable' => 'ativo',
            'id_integration' => 'id da integração',
            'id_webhook' => 'id da integração',
            'activecampaign_api_key' => 'chave da api activecampaign',
            'events' => 'quais eventos a integração será acionada'
        ];
    }
}