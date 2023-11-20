<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AffiliateSettingsRequest extends FormRequest
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
            'product_id' => 'required',
            'support_email' => 'required',
            'commission' => 'required',
            'cookie_duration' => ['required', Rule::in(['0', '1', '30', '60', '90', '180'])],
            'assignment' => ['required', Rule::in(['last_click', 'first_click'])]
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'product_id' => 'O Id do produto é obrigatório',
            'support_email' => 'O e-mail para suporte é obrigatório',
            'commission' => 'A comissão é obrigatória',
            'cookie_duration' => 'A duração dos cookies é obrigatório',
            'assignment' => 'A atribuição é obrigatória',
        ];
    }
}
