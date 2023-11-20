<?php

namespace App\Http\Requests\Checkout;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class SubscriberRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $isBr = $this->input('country') === 'BR';

        $phone_area_code_digits_lenght = $isBr ? 'digits:2' : 'digits_between:2,3';
        $phone_number_digits_lenght = $isBr ? 'digits_between:8,9' : 'digits_between:6,10';
        $br_required_nullable_otherwise = $isBr ? 'required' : 'nullable'; // required options only for BR

        return [
            'platform_id' => 'required',
            'plan_id' => 'required',
            'email' => 'required|email:strict,dns',
            'name' => 'required',
            'phone_country_code' => 'required|numeric|digits_between:1,3',
            'phone_area_code' => [
                'required',
                $phone_area_code_digits_lenght,
            ],
            'phone_number' => [
                'required',
                $phone_number_digits_lenght,
            ],
            'user_ip' => 'required|ip',
            'document_number' => [
                $br_required_nullable_otherwise,
                Rule::requiredIf(fn() => !is_null($this->input('document_type'))), // "cross" required
                'alpha_num', 'min:6', 'max:16',
            ],
            'document_type' => [
                $br_required_nullable_otherwise,
                Rule::requiredIf(fn() => !is_null($this->input('document_number'))), // "cross" required
                "in:cpf,cnpj,passport,other_natural,other_legal",
            ],
            'country' => 'required'
        ];
    }

    public function attributes()
    {
        return [
            'email' => 'E-mail',
            'name' => 'Nome',
            'phone_country_code' => 'Código telefônico do país',
            'phone_area_code' => 'Código telefônico de área',
            'phone_number' => 'Número de telefone',
            'user_ip' => 'Endereço IP',
            'document_number' => 'Número do documento',
            'document_type' => 'Tipo do documento',
            'country' => 'País'
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
