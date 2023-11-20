<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class FacebookPixelRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'url' => 'required|string',
            'client_ip_address' => 'required|string',
            'client_user_agent' => 'required|string',
        ];
    }
}
