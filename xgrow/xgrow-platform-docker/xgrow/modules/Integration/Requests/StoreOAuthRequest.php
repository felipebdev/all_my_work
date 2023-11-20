<?php

namespace Modules\Integration\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOAuthRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'code' => 'required',
            'state' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'code' => 'código de autorização oauth',
            'state' => 'dados adicionais oauth',
        ];
    }
}
