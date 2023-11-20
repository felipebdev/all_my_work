<?php

namespace App\Http\Requests;

use App\Client;
use App\Services\SerproApi\SerproApiService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateBankDataCoproducersAffiliations extends FormRequest
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
            'bank' => 'required',
            'branch' => 'required',
            'account' => 'required',
            'account_check_digit' => 'required',
            'holder_name' => 'required',
            'document' => [
                'required',
                'min:11',
                'max:14',
                function ($attribute, $value, $fail) {

                    $value = preg_replace('/[^0-9]/', '', $value);

                    if (strlen($value) === 14) {
                        if (cnpjValid($value) === false) {

                            $fail('O CNPJ ' . $value . ' digitado não é válido.');
                        } elseif (Client::where('cnpj', $value)->where('email', '<>', Auth::user()->email)->first()) {

                            $fail('O CNPJ ' . mask($value, '##.###.###/####-##') . ' já esta em uso.');
                        } elseif (SerproApiService::validateDocumentSerpro($value) === false) {

                            $fail('O CNPJ ' . mask($value, '##.###.###/####-##') . ' não esta em situação regular.');
                        }
                    } elseif (strlen($value) === 11) {
                        if (cpfValid($value) === false) {

                            $fail('O CPF ' . $value . ' digitado não é válido.');
                        } elseif (Client::where('cpf', $value)->where('email', '<>', Auth::user()->email)->first()) {

                            $fail('O CPF ' . mask($value, '###.###.###-##') . ' já esta em uso.');
                        }
                    }
                }
            ],
            'account_type' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!in_array($value, ['checking', 'savings'])) {
                        $fail('O Tipo de conta ' . $value . ' não é válido! Tipos de conta aceitos: checking - Corrente savings - Poupança');
                    }
                }
            ]
        ];
    }
}
