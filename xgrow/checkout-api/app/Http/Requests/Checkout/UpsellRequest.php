<?php

namespace App\Http\Requests\Checkout;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpsellRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'platform_id' => 'required',
            'plan_id' => 'required',
            'affiliate_id' => 'nullable',
            'payment_method' => [Rule::in('credit_card', 'boleto', 'pix'), 'required'],
            'subscriber_id' => 'required', // injected from middleware
            'cc_info' => ['array'],
            'cc_info.*.installment' => 'required_if:payment_method,credit_card',
            'cc_info.*.value' => 'required_if:payment_method,credit_card|numeric',
            'cc_info.*.brand' => 'required_if:payment_method,credit_card',
            'cc_info.*.last_four_digits' => 'required_if:payment_method,credit_card',
            'cc_info.*.card_id' => 'prohibited',
        ];
    }

    public function attributes()
    {
        return [
            'platform_id' => 'Código do produto',
            'plan_id' => 'Código do produto',
            'affiliate_id' => 'Código do afiliado',
            'payment_method' => 'Método de pagamento',
            'subscriber_id' => 'Código do subscriber',
            'cc_info' => 'Dados do cartão',
            'cc_info.*.value' => 'Valor do cartão',
            'cc_info.*.installment' => 'Número de parcelas',
            'cc_info.*.token' => 'Token',
            'cc_info.*.brand' => 'Bandeira',
            'cc_info.*.last_four_digits' => 'Últimos 4 dígitos',
        ];
    }

    /**
     * If validator fails return the exception in json form
     * @param  Validator  $validator
     * @return array
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['errors' => $validator->errors()], 400));
    }
}
