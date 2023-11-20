<?php

namespace App\Http\Requests\MyData;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
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
            'address' => 'required',
            'number' => 'required',
            'district' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zipcode' => 'required',
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
            'address' => 'O endereço é obrigatório',
            'number' => 'O número é obrigatório',
            'district' => 'O bairro é obrigatório',
            'city' => 'A cidade é obrigatório',
            'state' => 'O estado é obrigatório',
            'zipcode' => 'O CEP é obrigatório',
        ];
    }
}
