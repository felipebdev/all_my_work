<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MobileNotificationRequest extends FormRequest
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
            'read' => 'required|boolean',
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
            'read.required' => 'A confirmação ou negação da leitura da mensagem é um campo obrigatório.',
            'read.boolean' => 'O campo read só aceita valores booleanos, 0 e 1'
        ];
    }
}
