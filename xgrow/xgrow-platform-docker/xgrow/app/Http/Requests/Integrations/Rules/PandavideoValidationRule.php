<?php

namespace App\Http\Requests\Integrations\Rules;

use Illuminate\Validation\Rule;
use App\Http\Requests\Integrations\Rules\BaseRule;

class PandavideoValidationRule extends BaseRule
{

    public function getRules()
    {
        return [
            'name_integration' => 'required|max:190',
            'flag_enable' => 'boolean',
            'id_integration' => ['required', Rule::in(['16'])],
            'id_webhook' => ['required', Rule::in(['16'])],
            'pandavideo_api_key' => 'required',
        ];
    }

    public function getAttributes()
    {
        return [
            'name_integration' => 'nome da integração',
            'flag_enable' => 'ativo',
            'id_integration' => 'id da integração',
            'id_webhook' => 'id do webhook',
            'pandavideo_api_key' => 'chave da api da panda video'
        ];
    }
}
