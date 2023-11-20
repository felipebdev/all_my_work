<?php

namespace App\Http\Requests;

use App\Client;
use App\Services\SerproApi\SerproApiService;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class FirstAccessRequest extends FormRequest
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
            'first_name' => 'required',
            'last_name' => 'required',
            'type_person' => 'required',
            Rule::in(['J', 'F']),
            'document' => [
                'required',
                'min:11',
                'max:14',
                function ($attribute, $value, $fail) {
                    if (strlen($value) === 14) {

                        if (cnpjValid($value) === false) {

                            $fail('O CNPJ '.$value.' digitado não é válido.');
                        } elseif (Client::where('cnpj', $value)->first()) {

                            $fail('O CNPJ '.mask($value, '##.###.###/####-##').' já esta em uso.');
                        } elseif (SerproApiService::validateDocumentSerpro($value) === false) {

                            $fail('O CNPJ '.mask($value, '##.###.###/####-##').' não esta em situação regular.');
                        }
                    } elseif (strlen($value) === 11) {

                        if (cpfValid($value) === false) {

                            $fail('O CPF '.$value.' digitado não é válido.');
                        } elseif (Client::where('cpf', $value)->first()) {

                            $fail('O CPF '.mask($value, '###.###.###-##').' já esta em uso.');
                        }
                    }
                }
            ]
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
            'first_name.required' => 'O primeiro nome é um campo obrigatório.',
            'last_name.required' => 'O sobrenome é um campo obrigatório.',
            'type_person.required' => 'O tipo de pessoa é um campo obrigatório.',
        ];
    }
}
