<?php

namespace App\Http\Requests\Affiliation;

use App\Services\Finances\Objects\Constants;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class AffiliateStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'email' => 'required|email:strict,dns',
            'document_type' => [Rule::in('cpf','cnpj','passport','other_natural','other_legal'), 'required'],
            'document_number' => ['required','alpha_num', 'min:6', 'max:16'],
            'legal_name' => 'required',
            'account_type' => ['required', Rule::in([
                Constants::XGROW_ACCOUNT_TYPE_CHECKING,
                Constants::XGROW_ACCOUNT_TYPE_SAVINGS
            ])],
            'bank_code' => 'required',
            'agency' => 'required',
            'agency_digit' => ['nullable', 'string'],
            'account' => ['required', 'string'],
            'account_digit' => ['required', 'string', 'size:1'],
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
