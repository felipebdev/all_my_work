<?php

namespace App\Http\Requests\Checkout;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'platform_id' => 'required',
            'plan_id' => 'required',
            'affiliate_id' => 'nullable',
            'payment_method' => [Rule::in('credit_card', 'boleto', 'pix', 'multimeans'), 'required'],
            'subscriber_id' => 'required', // injected from middleware
            'cc_info' => ['array'],
            'cc_info.*.token' => 'required_if:payment_method,credit_card',
            'cc_info.*.installment' => 'required_if:payment_method,credit_card|integer|gt:0',
            'cc_info.*.value' => 'required_if:payment_method,credit_card|numeric',
            'cc_info.*.card_id' => 'prohibited',
            'payments' => ['array'],
            'payments.*.payment_method' => 'required_if:payment_method,multimeans',
            'payments.*.value' => 'required_if:payment_method,multimeans',
            'payments.*.token' => 'required_if:payments.*.payment_method,credit_card',
            'order_bump' => 'nullable|array',
            'cupom' => 'nullable|string',
        ];
    }

    public function attributes()
    {
        return [
            'platform_id' => 'Código da plataforma',
            'plan_id' => 'Código do produto',
            'affiliate_id' => 'Código do afiliado',
            'payment_method' => 'Método de pagamento',
            'subscriber_id' => 'Código do subscriber',
            'cc_info' => 'Dados do cartão',
            'cc_info.*.value' => 'Valor do cartão',
            'cc_info.*.installment' => 'Número de parcelas',
            'cc_info.*.token' => 'Token',
            'payments.*.payment_method' => 'Método de pagamento',
            'payments.*.value' => 'Valor',
            'payments.*.token' => 'Token',
            'order_bump' => 'Order Bump',
            'cupom' => 'Cupom',
        ];
    }

    public function messages()
    {
        return [
            'array' => ':attribute deve ser um array',
            'required_if' => ':attribute obrigatório para método de pagamento selecionado',
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
