<?php

namespace App\Http\Requests\MyData;

use App\Http\Traits\CustomResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use App\Client;
use App\Services\SerproApi\SerproApiService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class IdentityRequest extends FormRequest
{

    use CustomResponseTrait;

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
            //Identity validation data
            'file' => 'required|mimes:jpeg,bmp,png,jpg,pdf',
            'company_name' => 'required_if:type_person,J',
            'first_name' => 'required_if:type_person,F',
            'last_name' => 'required_if:type_person,F',
            'type_person' => 'required|in:F,J',
            'document' => [
                'required',
                'min:11',
                'max:14',
                function ($attribute, $value, $fail) {
                    return $this->validateDocument($attribute, $value, $fail);
                }
            ],
            //Bank account data
            'bank_code' => 'required|digits:3',
            'agency' => 'required|digits_between:1,4|gt:0',
            'agency_digit' => 'nullable|digits:1',
            'account' => 'required|digits_between:1,13',
            'account_digit' => 'required|digits_between:1,2',
            'account_type' => 'required|in:checking,savings',
            'document_type' => 'required|in:cpf,cnpj',
            'document_number' => 'required|min:11|max:14|same:document',
            'legal_name' => 'required|max:30',
        ];
    }

    public function attributes()
    {
        return [
            'file' => 'Foto do documento',
            'company_name' => 'Razão social da identificação',
            'first_name' => 'Primeiro nome',
            'last_name' => 'Último nome',
            'type_person' => 'Tipo de pessoa',
            'document' => 'Número do documento de identificação',
            //Bank account data
            'bank_code' => 'Código bancário',
            'agency' => 'Agência bancária',
            'agency_digit' => 'Digito verificador da agência bancária',
            'account' => 'Conta bancária',
            'account_digit' => 'Digito verificador da conta bancária',
            'account_type' => 'Tipo de conta bancária',
            'document_type' => 'Tipo de documento bancário',
            'document_number' => 'Documento bancário',
            'legal_name' => 'Razão social/Nome do titular nos dados bancários',
        ];
    }

    private function validateDocument($attribute, $value, $fail) {
        if (strlen($value) === 14) {

            if (cnpjValid($value) === false) {

                $fail('O CNPJ '.$value.' digitado não é válido.');
            } elseif (Client::where('cnpj', $value)->where('email', '<>', Auth::user()->email)->first()) {

                $fail('O CNPJ '.mask($value, '##.###.###/####-##').' já esta em uso.');
            } elseif (SerproApiService::validateDocumentSerpro($value) === false) {

                $fail('O CNPJ '.mask($value, '##.###.###/####-##').' não esta em situação regular.');
            }
        } elseif (strlen($value) === 11) {

            if (cpfValid($value) === false) {

                $fail('O CPF '.$value.' digitado não é válido.');
            } elseif (Client::where('cpf', $value)->where('email', '<>', Auth::user()->email)->first()) {

                $fail('O CPF '.mask($value, '###.###.###-##').' já esta em uso.');
            }
        }
    }

    protected function failedValidation(Validator $validator)
    {
        $message = "";
        foreach($validator->getMessageBag()->getMessages() as $field) {
            foreach($field as $fieldMessage) {
                $message .= $fieldMessage;
            }
        }
        throw new HttpResponseException($this->customJsonResponse($message, 400, $validator->getMessageBag()->getMessages()));
    }

}
