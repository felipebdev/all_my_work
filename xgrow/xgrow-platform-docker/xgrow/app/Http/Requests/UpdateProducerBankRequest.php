<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProducerBankRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'holder_name' => 'required',
            'document' => 'required',
            'account_type' => 'required',
            'bank' => 'required',
            'branch' => 'required',
            'branch_check_digit' => 'required',
            'account' => 'required',
            'account_check_digit' => 'required',
        ];
    }
}
