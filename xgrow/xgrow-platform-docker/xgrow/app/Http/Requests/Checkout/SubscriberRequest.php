<?php

namespace App\Http\Requests\Checkout;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class SubscriberRequest extends FormRequest
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
            'email' => 'required|email',
            'name' => 'required',
            'phone_country_code' => 'required|numeric|digits_between:1,3',
            'phone_area_code' => 'required|numeric|digits_between:2,3',
            'phone_number' => 'required|numeric|digits_between:6,10',
            'user_ip' => 'required|ip',
            'document_number' => 'required|numeric',
            'document_type' => [ Rule::in('cpf','cnpj','passport'), 'required']
        ];
    }

    public function attributes()
    {
        return [
          'email' => 'E-mail',
          'name' => 'Nome',
          'phone_country_code' => 'Código do país (Telefone)',
          'phone_area_code' => 'Código de área (Telefone)',
          'phone_number' => 'Número de telefone',
          'document_number' => 'Documento',
          'document_type' => 'Tipo do documento',
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
