<?php

namespace App\Http\Requests\Checkout;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
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
            'payment_method' => [ Rule::in('credit_card','boleto','pix'), 'required'],
            'cc_info' => ['array'],
            'cc_info.*.token' => 'required_if:payment_method,credit_card',
            'cc_info.*.installment' => 'required_if:payment_method,credit_card',
            'cc_info.*.value' => 'required_if:payment_method,credit_card|numeric',
        ];
    }

    public function attributes()
    {
        return [
            'platform_id' => 'Código do produto',
            'plan_id' => 'Código do produto',
            'payment_method' => 'Método de pagamento',
            'cc_info' => 'Dados do cartão',
            'cc_info.*.value' => 'Valor do cartão',
            'cc_info.*.installment' => 'Número de parcelas',
            'cc_info.*.token' => 'Token',
        ];
    }

    public function messages()
    {
        return [

        ];
    }

    /**
     * If validator fails return the exception in json form
     * @param Validator $validator
     * @return array
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['errors' => $validator->errors()], 400));
    }
}
