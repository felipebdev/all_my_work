<?php

namespace App\Http\Requests\Integrations\Rules;

use Illuminate\Validation\Rule;
use App\Http\Requests\Integrations\Rules\BaseRule;

class FandoneValidationRule extends BaseRule {

    public function getRules() {
        return [
            'name_integration' => 'required|max:190',
            'days_limit_payment_pendent' => 'required|integer',
            'flag_enable' => 'boolean',
            'trigger_email' => 'boolean',
            'id_integration' => ['required', Rule::in(['5'])],
            'id_webhook' => ['required', Rule::in(['5'])],
            'prod_count_id' => 'required',
            'prod_public_key' => 'required',
            'prod_secret_key' => 'required',
            'homol_count_id' => 'required',
            'homol_public_key' => 'required',
            'homol_secret_key' => 'required',
        ];
    }

    public function getAttributes() {
        return [
            'name_integration' => 'nome da integração',
            'days_limit_payment_pendent' => 'dias limite de pagamento pendente',
            'flag_enable' => 'ativo',
            'id_integration' => 'id da integração',
            'id_webhook' => 'id da integração',
            'prod_count_id' => 'id da conta de produção',
            'prod_public_key' => 'chave pública de produção',
            'prod_secret_key' => 'chave privada de produção',
            'homol_count_id' => 'id da conta de homologação',
            'homol_public_key' => 'chave pública de homologação',
            'homol_secret_key' => 'chave privada de homologação',
        ];
    }
}