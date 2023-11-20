<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBankAccountRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'bank_code' => [
                'required', 'string', 'min:3',
            ],
            'agency' => [
                'required', 'string',
            ],
            'agency_digit' => [
                'nullable', 'string',
            ],
            'account' => [
                'required', 'string',
            ],
            'account_digit' => [
                'required', 'string', 'size:1',
            ],
            'account_type' => [
                'required', 'string',
            ],
            'document_number' => [
                'required', 'string', 'min:11', 'max:14',
            ],
            'legal_name' => [
                'required', 'string', 'min:3',
            ],
        ];
    }
}
