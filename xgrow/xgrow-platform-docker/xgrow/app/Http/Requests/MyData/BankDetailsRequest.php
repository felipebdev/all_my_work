<?php

namespace App\Http\Requests\MyData;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class BankDetailsRequest extends FormRequest
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

    public function prepareForValidation()
    {
        $user = Auth::user();
        $this->merge([
            'user_id' => $user->id,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $user = Auth::user();
        $data =  [
            'two_factor_code' => 'required',
            'bank_code' => 'required|digits:3',
            'agency' => 'required|digits_between:1,4|gt:0',
            'agency_digit' => 'nullable|digits:1',
            'account' => 'required|digits_between:1,13',
            'account_digit' => 'required|digits_between:1,2',
            'account_type' => 'required|in:checking,savings',
            //'document_type' => 'required|in:cpf,cnpj',
            'document_number' => 'required|min:11|max:14',
            'legal_name' => 'required'
        ];

        return $data;
    }

    public function messages()
    {
        return [
            'user_id.unique' => 'Usuário já possui informações bancárias cadastradas',
        ];
    }


}
