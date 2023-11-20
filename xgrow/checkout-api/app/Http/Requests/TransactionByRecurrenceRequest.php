<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransactionByRecurrenceRequest extends FormRequest
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

    public function rules()
    {
        return [
            'payment_method' => [
                'required',
                Rule::in(['credit_card', 'boleto', 'pix']),
            ],
            'cc_info.token' => 'required_if:payment_method,credit_card',
            'cc_info.installment' => 'required_if:payment_method,credit_card',
            'cc_info.value' => 'required_if:payment_method,credit_card'
        ];
    }
}
