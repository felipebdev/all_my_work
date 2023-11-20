<?php

namespace App\Http\Requests;

use App\Services\Finances\Objects\Constants;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SendTwoFactorCodeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'order_code' => 'required',
            'email' => 'required'
        ];
    }

}
