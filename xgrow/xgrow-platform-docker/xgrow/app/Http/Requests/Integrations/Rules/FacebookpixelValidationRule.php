<?php

namespace App\Http\Requests\Integrations\Rules;

use Illuminate\Validation\Rule;
use App\Http\Requests\Integrations\Rules\BaseRule;

class FacebookpixelValidationRule extends BaseRule {

    public function getRules() {
        return [
            'name_integration' => 'required|max:190',
            'pixel_id' => 'required',
            'pixel_token' => 'required',
            'pixel_test_event_code' => 'nullable|string',
            'flag_enable' => 'boolean',
            'id_integration' => ['required', Rule::in(['9'])],
            'id_webhook' => ['required', Rule::in(['9'])],
        ];
    }

    public function getAttributes() {
        return [
            'name_integration' => 'nome da integração',
            'pixel_id' => 'id do pixel facebook',
            'pixel_token' => 'token do pixel facebook',
            'pixel_test_event_code' => 'código de evento de teste',
            'flag_enable' => 'ativo',
            'id_integration' => 'id da integração',
            'id_webhook' => 'id da integração'
        ];
    }
}
