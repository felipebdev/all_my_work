<?php

namespace App\Http\Requests\Api\Student;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudentPaymentChangeRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'payment_id' => 'required',
            'payment_method' => [Rule::in('credit_card', 'boleto', 'pix'), 'required'],
            'cc_info' => ['array'],
            'cc_info.token' => 'required_if:payment_method,credit_card',
        ];
    }

    public function attributes()
    {
        return [
            'payment_id' => 'Código do pagamento',
            'payment_method' => 'Método de pagamento',
            'subscriber_id' => 'Código do subscriber',
            'cc_info' => 'Dados do cartão',
            'cc_info.token' => 'Token',
        ];
    }
}
