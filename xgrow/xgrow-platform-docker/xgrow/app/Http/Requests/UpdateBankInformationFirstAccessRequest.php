<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBankInformationFirstAccessRequest extends FormRequest
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
            'holder_name' => 'required|min:5|max:30',
            'bank' => 'required',
            'branch' => 'required',
            'branch_check_digit' => 'nullable',
            'account' => 'required',
            'account_check_digit' => 'required',
            'account_type' => ['required', Rule::in(['checking', 'savings'])]
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
            'holder_name.min' => 'O campo nome do titular da conta não pode ser superior a 5 caracteres..',
            'holder_name.max' => 'O campo nome do titular da conta não pode ser superior a 30 caracteres..',
            'holder_name.required' => 'O campo nome do titular da conta é obrigatório..',
            'branch.required' => 'O campo agência é obrigatório..',
            'account.required' => 'O campo conta é obrigatório..',
            'account_check_digit.required' => 'O campo dígito de verificação de conta é obrigatório..',
            'account_type.required' => 'O campo tipo da conta é obrigatório..',
        ];
    }
}
