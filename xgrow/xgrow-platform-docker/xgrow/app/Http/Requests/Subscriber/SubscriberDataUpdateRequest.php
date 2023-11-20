<?php

namespace App\Http\Requests\Subscriber;

use Illuminate\Foundation\Http\FormRequest;

class SubscriberDataUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|min:1', // min 1 char if provided
            'email' => 'required|email:strict,dns',
            'cel_phone' => 'string|nullable', // user can unset cel_phone
            'raw_password' => 'string|confirmed|min:6', // optional password, password_confirmation required if provided
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Nome completo',
            'email' => 'Email',
            'cel_phone' => 'Celular',
            'raw_password' => 'Senha',
        ];
    }
}
